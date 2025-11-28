<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DashboardController extends Controller
{
    //
    public function userDashboard()
    {
        $user = auth()->user();

        $stats = [
            'totalOrders' => Order::where('user_id', $user->id)->count(),
            'inProgress' => Order::where('user_id', $user->id)
                            ->whereIn('status', ['progress', 'accepted'])
                            ->count(),
            'completed' => Order::where('user_id', $user->id)
                            ->where('status', 'completed')
                            ->count(),
            'feedbacks' => Feedback::where('user_id', $user->id)->count(),
        ];

        $recentOrders = Order::where('user_id', $user->id)
                            ->with('package')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact('stats', 'recentOrders'));
    }

    public function adminDashboard()
    {
        $stats = [
            'totalUsers' => User::where('role', 'user')->count(),
            'newOrders' => Order::where('status', 'pending')->count(),
            'inProgress' => Order::whereIn('status', ['progress', 'accepted'])->count(),
            'newFeedbacks' => Feedback::where('is_read', false)->count(),
            'revenue' => Order::where('status', 'completed')
                            ->whereMonth('created_at', now()->month)
                            ->sum('total_price'),
        ];

        $recentOrders = Order::with(['user', 'package'])
                            ->latest()
                            ->take(5)
                            ->get();

        $recentUsers = User::where('role', 'user')
                          ->latest()
                          ->take(5)
                          ->get();

        return view('dashboard-admin', compact('stats', 'recentOrders', 'recentUsers'));
    }
}
