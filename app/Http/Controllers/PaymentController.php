<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Handle POST callback (server-to-server notification) from Midtrans
     */
    public function callback(Request $request)
    {
        Log::info('=== MIDTRANS POST CALLBACK START ===');
        Log::info('POST Callback Data:', $request->all());

        try {
            // Get notification data
            $notification = json_decode($request->getContent(), true);

            // If empty, try to get from form data
            if (empty($notification)) {
                $notification = $request->all();
            }

            Log::info('Processed Notification:', $notification);

            // Validate required fields
            if (empty($notification['order_id']) || empty($notification['transaction_status'])) {
                Log::error('Missing required fields in callback', $notification);

                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $statusCode = $notification['status_code'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;
            $paymentType = $notification['payment_type'] ?? null;
            $merchantId = $notification['merchant_id'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $transactionId = $notification['transaction_id'] ?? null;
            $signatureKey = $notification['signature_key'] ?? null;

            // Find order by order_number (Midtrans order_id)
            $order = Order::where('order_number', $orderId)->first();

            if (! $order) {
                Log::error('Order not found for callback - Order Number: '.$orderId);

                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            Log::info('Found Order:', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'current_payment_status' => $order->payment_status,
                'current_order_status' => $order->status,
            ]);

            // Verify signature (optional but recommended)
            // $serverKey = config('services.midtrans.server_key');
            // $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            // if ($signatureKey !== $expectedSignature) {
            //     Log::error('Invalid signature for order: ' . $orderId);
            //     return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            // }

            DB::beginTransaction();

            $previousPaymentStatus = $order->payment_status;
            $previousOrderStatus = $order->status;

            // Update order based on transaction status
            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        // Transaction is challenged by FDS
                        $order->update([
                            'payment_status' => Order::PAYMENT_PENDING,
                            'status' => Order::STATUS_PENDING,
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran perlu verifikasi manual - Status: Challenge via '.$paymentType,
                        ]);
                        Log::info('Payment challenged for order: '.$order->order_number);
                    } elseif ($fraudStatus == 'accept') {
                        // Transaction is successful
                        $order->update([
                            'payment_status' => Order::PAYMENT_PAID,
                            'status' => Order::STATUS_CONFIRMED,
                            'paid_amount' => $grossAmount,
                            'paid_at' => now(),
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran berhasil via '.$paymentType,
                        ]);
                        Log::info('Payment successful for order: '.$order->order_number);
                    }
                    break;

                case 'settlement':
                    // Transaction is successful
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                        'status' => Order::STATUS_PENDING, // Tetap PENDING, tunggu konfirmasi admin
                        'paid_amount' => $grossAmount,
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran berhasil diselesaikan via '.$paymentType.'. Menunggu konfirmasi admin.',
                    ]);
                    Log::info('Payment settled for order: '.$order->order_number);
                    break;

                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        // Transaction is challenged by FDS
                        $order->update([
                            'payment_status' => Order::PAYMENT_PENDING,
                            'status' => Order::STATUS_PENDING,
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran perlu verifikasi manual - Status: Challenge via '.$paymentType,
                        ]);
                        Log::info('Payment challenged for order: '.$order->order_number);
                    } elseif ($fraudStatus == 'accept') {
                        // Transaction is successful
                        $order->update([
                            'payment_status' => Order::PAYMENT_PAID,
                            'status' => Order::STATUS_PENDING, // Tetap PENDING, tunggu konfirmasi admin
                            'paid_amount' => $grossAmount,
                            'paid_at' => now(),
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran berhasil via '.$paymentType.'. Menunggu konfirmasi admin.',
                        ]);
                        Log::info('Payment successful for order: '.$order->order_number);
                    }
                    break;

                case 'deny':
                    $order->update([
                        'payment_status' => Order::PAYMENT_FAILED,
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran ditolak via '.$paymentType,
                    ]);
                    Log::info('Payment denied for order: '.$order->order_number);
                    break;

                case 'cancel':
                    $order->update([
                        'payment_status' => Order::PAYMENT_FAILED,
                        'status' => $order->status === Order::STATUS_CONFIRMED ? $order->status : Order::STATUS_CANCELLED,
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran dibatalkan via '.$paymentType,
                    ]);
                    Log::info('Payment cancelled for order: '.$order->order_number);
                    break;

                case 'expire':
                    $order->update([
                        'payment_status' => Order::PAYMENT_EXPIRED,
                        'status' => $order->status === Order::STATUS_CONFIRMED ? $order->status : Order::STATUS_CANCELLED,
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran kadaluarsa via '.$paymentType,
                    ]);
                    Log::info('Payment expired for order: '.$order->order_number);
                    break;

                default:
                    Log::warning('Unknown transaction status: '.$transactionStatus);
                    break;
            }

            DB::commit();

            // Reload order to get updated values
            $order->refresh();

            Log::info('Payment callback processed successfully for order: '.$order->order_number, [
                'previous_payment_status' => $previousPaymentStatus,
                'new_payment_status' => $order->payment_status,
                'previous_order_status' => $previousOrderStatus,
                'new_order_status' => $order->status,
                'transaction_status' => $transactionStatus,
                'paid_at' => $order->paid_at,
                'paid_amount' => $order->paid_amount,
            ]);

            // Return success response for Midtrans
            return response()->json([
                'status' => 'success',
                'message' => 'Callback processed',
                'order_id' => $order->order_number,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment callback: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error: '.$e->getMessage(),
            ], 500);
        } finally {
            Log::info('=== MIDTRANS POST CALLBACK END ===');
        }
    }

    /**
     * Handle GET callback/finish URL from Midtrans (user redirect)
     */
    public function callbackFinish(Request $request)
    {
        Log::info('=== MIDTRANS FINISH CALLBACK START ===');
        Log::info('GET Finish Callback Data:', $request->all());

        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');
        $statusCode = $request->get('status_code');
        $paymentType = $request->get('payment_type');

        if (! $orderId) {
            Log::error('No order_id in finish URL');

            return redirect()->route('user.dashboard')->with('error', 'Data callback tidak valid');
        }

        // Find order by order_number
        $order = Order::where('order_number', $orderId)->first();

        if (! $order) {
            Log::error('Order not found for finish URL - Order Number: '.$orderId);

            return redirect()->route('user.dashboard')->with('error', 'Pesanan tidak ditemukan');
        }

        Log::info('Finish URL Order Status:', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'current_payment_status' => $order->payment_status,
            'current_order_status' => $order->status,
            'callback_transaction_status' => $transactionStatus,
        ]);

        // Double-check and update status if needed
        $this->syncOrderStatus($order, $transactionStatus, $request);

        // Redirect user to appropriate page
        return $this->redirectUserBasedOnStatus($order, $transactionStatus);
    }

    /**
     * Sync order status with Midtrans notification
     */
    private function syncOrderStatus(Order $order, $transactionStatus, Request $request)
    {
        try {
            // If order is already paid, no need to update
            if ($order->payment_status === Order::PAYMENT_PAID) {
                Log::info('Order already paid, skipping update');

                return;
            }

            // Get additional data from request
            $fraudStatus = $request->get('fraud_status');
            $grossAmount = $request->get('gross_amount');
            $transactionId = $request->get('transaction_id');
            $merchantId = $request->get('merchant_id');
            $paymentType = $request->get('payment_type');

            // Update based on transaction status
            switch ($transactionStatus) {
                case 'settlement':
                case 'capture':
                    if ($fraudStatus !== 'challenge') {
                        $order->update([
                            'payment_status' => Order::PAYMENT_PAID,
                            'status' => Order::STATUS_CONFIRMED,
                            'paid_amount' => $grossAmount ?? $order->total_price,
                            'paid_at' => now(),
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran berhasil via '.$paymentType.' (from finish URL)',
                        ]);
                        Log::info('Order status updated to PAID from finish URL');
                    }
                    break;

                case 'pending':
                    $order->update([
                        'payment_status' => Order::PAYMENT_PENDING,
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Menunggu pembayaran via '.$paymentType.' (from finish URL)',
                    ]);
                    Log::info('Order status updated to PENDING from finish URL');
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                    $order->update([
                        'payment_status' => $transactionStatus === 'expire' ? Order::PAYMENT_EXPIRED : Order::PAYMENT_FAILED,
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran '.$transactionStatus.' via '.$paymentType.' (from finish URL)',
                    ]);
                    Log::info('Order status updated to '.$transactionStatus.' from finish URL');
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error syncing order status: '.$e->getMessage());
        }
    }

    /**
     * Redirect user based on payment status
     */
    private function redirectUserBasedOnStatus(Order $order, $transactionStatus)
    {
        // First, check current payment status
        switch ($order->payment_status) {
            case Order::PAYMENT_PAID:
                Log::info('Redirecting to SUCCESS page for order: '.$order->order_number);

                return redirect()->route('payment.success', ['order' => $order->id])
                    ->with('success', 'Pembayaran berhasil! Terima kasih.');

            case Order::PAYMENT_PENDING:
                Log::info('Redirecting to PENDING page for order: '.$order->order_number);

                return redirect()->route('payment.pending', ['order' => $order->id])
                    ->with('info', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.');

            case Order::PAYMENT_FAILED:
            case Order::PAYMENT_EXPIRED:
                Log::info('Redirecting to FAILED page for order: '.$order->order_number);

                return redirect()->route('payment.failed', ['order' => $order->id])
                    ->with('error', 'Pembayaran '.strtolower($order->payment_status_display_name));

            default:
                // Fallback based on transaction status from callback
                switch ($transactionStatus) {
                    case 'settlement':
                    case 'capture':
                        return redirect()->route('payment.success', ['order' => $order->id]);
                    case 'pending':
                        return redirect()->route('payment.pending', ['order' => $order->id]);
                    default:
                        return redirect()->route('payment.failed', ['order' => $order->id]);
                }
        }
    }
}
