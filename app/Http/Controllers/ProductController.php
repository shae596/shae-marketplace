<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->status === 'approved' || Auth::id() === $product->user_id, 404);

        $product->load(['category', 'vendor']);

        return view('products.show', compact('product'));
    }

    public function search(Request $request)
    {
        $products = Product::approved()
            ->search($request->get('q'))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->limit(10)
            ->get(['id', 'name', 'price', 'slug']);

        return response()->json(['data' => $products]);
    }
}
