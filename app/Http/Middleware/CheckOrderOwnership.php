<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrderOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Get order ID from route parameters
        $orderId = $request->route('order');

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // If no order ID provided, continue (might be handled by controller)
        if (!$orderId) {
            return $next($request);
        }

        // Find the order
        $order = Order::find($orderId);

        if (!$order) {
            abort(404, 'Order not found.');
        }

        // Check if user owns the order or is admin
        if ($order->user_id === $user->id || $user->role === 'admin') {
            return $next($request);
        }

        // If user doesn't own the order, show 403 error
        abort(403, 'Unauthorized action. You do not have permission to access this order.');
    }
}
