<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;

        foreach ($products as $product) {
            $total += $product->price * $cart[$product->id];
        }

        return view('client.cart', compact('products', 'cart', 'total'));
    }

    public function add(Product $product)
    {
        abort_unless($product->status === 'approved' && $product->stock > 0, 404);

        $cart = session('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + 1;
        session(['cart' => $cart]);

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produit retiré du panier.');
    }
}
