<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Mail\StatusNotificationMail;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['vendor', 'category'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->search($request->get('q'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('gestionnaire.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('gestionnaire.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = $data['status'] ?? 'approved';

        if ($request->file('image')?->isValid()) {
            $data['image'] = $this->storeProductImage($request);
        }

        Product::create($data);

        return redirect()->route('gestionnaire.products.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('gestionnaire.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->file('image')?->isValid()) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $this->storeProductImage($request);
        }

        $product->update($data);

        return redirect()->route('gestionnaire.products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('gestionnaire.products.index')->with('success', 'Produit supprimé.');
    }

    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected,pending'],
        ]);

        $product->update(['status' => $request->status]);

        Mail::to($product->vendor->email)->send(
            new StatusNotificationMail($product->vendor, 'Statut produit mis à jour: '.$request->status)
        );

        return back()->with('success', 'Statut du produit mis à jour.');
    }

    private function storeProductImage(Request $request): string
    {
        Storage::disk('public')->makeDirectory('products');

        return $request->file('image')->store('products', 'public');
    }
}
