<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function checkoutForm()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Votre panier est vide.']);
        }

        return view('client.checkout');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_phone' => ['required', 'string', 'max:20'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Votre panier est vide.']);
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;

        foreach ($products as $product) {
            $total += $product->price * $cart[$product->id];
        }

        $order = Order::create([
            'reference' => 'SHAE-'.strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'shipping_phone' => $request->shipping_phone,
        ]);

        foreach ($products as $product) {
            $qty = $cart[$product->id];
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $product->price,
                'subtotal' => $product->price * $qty,
            ]);
        }

        session()->forget('cart');

        Mail::to(auth()->user()->email)->send(new OrderConfirmedMail(auth()->user(), $order));

        return redirect()->route('payments.initiate', $order);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load(['items.product', 'payment']);

        return view('client.orders.show', compact('order'));
    }
}
