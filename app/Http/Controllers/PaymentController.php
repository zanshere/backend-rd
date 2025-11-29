<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $transactionStatus = $request->get('transaction_status');

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::error('Order not found for callback: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update order status based on payment status
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'status' => Order::STATUS_ACCEPTED,
                    'paid_amount' => $order->total_price, // Full payment
                    'paid_at' => now(),
                ]);
                break;

            case 'pending':
                $order->update([
                    'payment_status' => Order::PAYMENT_PENDING,
                ]);
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                    'status' => Order::STATUS_CANCELLED,
                ]);
                break;
        }

        // Redirect user based on payment status
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            return redirect()->route('user.orders')->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        } else {
            return redirect()->route('payment', ['order' => $order->id])->with('error', 'Pembayaran gagal atau tertunda.');
        }
    }
}
