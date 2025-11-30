<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Received:', $request->all());

        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $transactionStatus = $request->get('transaction_status');
        $fraudStatus = $request->get('fraud_status');
        $paymentType = $request->get('payment_type');
        $merchantId = $request->get('merchant_id');

        // Cari order berdasarkan order_number
        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::error('Order not found for callback - Order ID: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        Log::info('Processing payment callback for order: ' . $order->order_number, [
            'transaction_status' => $transactionStatus,
            'status_code' => $statusCode,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
            'merchant_id' => $merchantId
        ]);

        try {
            DB::beginTransaction();

            // Update order berdasarkan status transaksi
            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        // Transaction is challenged by FDS
                        $order->update([
                            'payment_status' => Order::PAYMENT_PENDING,
                            'status' => Order::STATUS_PENDING,
                            'midtrans_transaction_id' => $request->get('transaction_id'),
                            'admin_notes' => 'Pembayaran perlu verifikasi manual - Status: Challenge',
                        ]);
                    } else if ($fraudStatus == 'accept') {
                        // Transaction is successful
                        $order->update([
                            'payment_status' => Order::PAYMENT_PAID,
                            'status' => Order::STATUS_ACCEPTED,
                            'paid_amount' => $order->total_price,
                            'paid_at' => now(),
                            'midtrans_transaction_id' => $request->get('transaction_id'),
                            'midtrans_merchant_id' => $merchantId,
                            'admin_notes' => 'Pembayaran berhasil via ' . $paymentType,
                        ]);
                    }
                    break;

                case 'settlement':
                    // Transaction is successful
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                        'status' => Order::STATUS_ACCEPTED,
                        'paid_amount' => $order->total_price,
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $request->get('transaction_id'),
                        'midtrans_merchant_id' => $merchantId,
                        'admin_notes' => 'Pembayaran berhasil diselesaikan via ' . $paymentType,
                    ]);
                    break;

                case 'pending':
                    // Customer has not completed payment
                    $order->update([
                        'payment_status' => Order::PAYMENT_PENDING,
                        'midtrans_transaction_id' => $request->get('transaction_id'),
                        'admin_notes' => 'Menunggu pembayaran via ' . $paymentType,
                    ]);
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                    // Transaction is failed/cancelled/expired
                    $order->update([
                        'payment_status' => Order::PAYMENT_FAILED,
                        'status' => Order::STATUS_CANCELLED,
                        'midtrans_transaction_id' => $request->get('transaction_id'),
                        'admin_notes' => 'Pembayaran ' . $transactionStatus . ' via ' . $paymentType,
                    ]);
                    break;

                default:
                    Log::warning('Unknown transaction status: ' . $transactionStatus);
                    break;
            }

            DB::commit();

            Log::info('Payment callback processed successfully for order: ' . $order->order_number, [
                'new_payment_status' => $order->payment_status,
                'new_order_status' => $order->status
            ]);

            // Redirect user berdasarkan status pembayaran
            if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus == 'accept') {
                return redirect()->route('user.orders')->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
            } elseif ($transactionStatus == 'pending') {
                return redirect()->route('payment', ['order' => $order->id])->with('info', 'Pembayaran tertunda. Silakan selesaikan pembayaran Anda.');
            } else {
                return redirect()->route('payment', ['order' => $order->id])->with('error', 'Pembayaran gagal atau dibatalkan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment callback: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
