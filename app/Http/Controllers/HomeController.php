<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor'])
            ->approved()
            ->search($request->get('q'));

        $activeCategory = null;

        if ($request->filled('category_id')) {
            $activeCategory = Category::find($request->category_id);

            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('home', compact('products', 'activeCategory'));
    }
}
