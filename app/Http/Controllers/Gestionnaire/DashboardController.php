<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products_count' => Product::count(),
            'approved_products' => Product::where('status', 'approved')->count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'orders_processing' => Order::whereIn('status', ['paid', 'processing'])->count(),
            'sales_count' => OrderItem::count(),
            'revenue' => OrderItem::sum('subtotal'),
        ];

        return view('gestionnaire.dashboard', compact('stats'));
    }
}
