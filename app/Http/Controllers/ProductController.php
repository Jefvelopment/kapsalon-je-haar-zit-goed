<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductView;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(protected RecommendationService $recommendations)
    {
    }

    public function index()
    {
        $products = Product::all();
        $recommended = $this->recommendations->recommendedProducts();

        return view('products.index', compact('products', 'recommended'));
    }

    public function view($id)
    {
        $product = Product::findOrFail((int) $id);

        ProductView::create([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
        ]);

        return view('products.view', compact('product'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'stock'    => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'image'    => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($validated['image']);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product toegevoegd.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail((int) $id);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail((int) $id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'stock'    => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'image'    => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($validated['image']);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product bijgewerkt.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail((int) $id);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product verwijderd.');
    }
}