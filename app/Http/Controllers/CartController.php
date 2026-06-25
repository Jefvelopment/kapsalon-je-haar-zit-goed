<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected const SESSION_KEY = 'cart';

    public function index()
    {
        $cart = session(self::SESSION_KEY, []);

        $items = collect($cart)
            ->map(function ($quantity, $productId) {
                $product = Product::find($productId);

                if (!$product) {
                    return null;
                }

                return [
                    'product'  => $product,
                    'quantity' => (int) $quantity,
                    'subtotal' => $product->price * (int) $quantity,
                ];
            })
            ->filter()
            ->values();

        $total = $items->sum('subtotal');

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $productId = (int) $productId;

        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        $quantity = $validated['quantity'] ?? 1;

        $cart = session(self::SESSION_KEY, []);

        $currentQuantity = (int) ($cart[$productId] ?? 0);
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            return redirect()->back()->withErrors([
                'stock' => "Niet genoeg voorraad voor {$product->name}.",
            ]);
        }

        $cart[$productId] = $newQuantity;

        session([self::SESSION_KEY => $cart]);

        return redirect()->back()->with('success', 'Product toegevoegd aan mandje.');
    }

    public function update(Request $request, $productId)
    {
        $productId = (int) $productId;

        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $quantity = (int) $validated['quantity'];

        if ($quantity > $product->stock) {
            return redirect()->back()->withErrors([
                'stock' => "Niet genoeg voorraad voor {$product->name}.",
            ]);
        }

        $cart = session(self::SESSION_KEY, []);
        $cart[$productId] = $quantity;

        session([self::SESSION_KEY => $cart]);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Mandje bijgewerkt.');
    }

    public function remove($productId)
    {
        $productId = (int) $productId;

        $cart = session(self::SESSION_KEY, []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session([self::SESSION_KEY => $cart]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Product verwijderd uit mandje.');
    }
}