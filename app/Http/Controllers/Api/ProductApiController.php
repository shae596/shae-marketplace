<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'vendor'])
            ->approved()
            ->search($request->get('q'))
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Liste des produits',
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'Détail du produit',
            'data' => $product->load(['category', 'vendor']),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']).'-'.\Illuminate\Support\Str::random(6);
        $data['status'] = 'pending';

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produit créé',
            'data' => $product,
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        abort_unless($product->user_id === $request->user()->id, 403);

        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produit mis à jour',
            'data' => $product,
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->user_id === $request->user()->id, 403);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé',
            'data' => null,
        ]);
    }
}
