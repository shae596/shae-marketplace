<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'products_count' => Product::count(),
            'orders_count' => Order::count(),
            'revenue' => Payment::where('status', 'success')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $recentOrders = Order::with('user')->latest()->limit(5)->get();
        $monthlySales = Payment::where('status', 'success')
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->whereYear('paid_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentOrders', 'monthlySales'));
    }
}
