<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/payment/status/{order}', function (Order $order) {
        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $order->payment_status,
            'order_number' => $order->order_number,
            'paid_at' => $order->paid_at,
            'payment_url' => $order->payment_url,
            'overall_progress' => $order->overall_progress
        ]);
    });
});
