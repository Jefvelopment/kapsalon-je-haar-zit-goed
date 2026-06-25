<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected const SESSION_KEY = 'cart';

    public function create()
    {
        $cart = session(self::SESSION_KEY, []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'Je mandje is leeg.']);
        }

        return view('orders.create');
    }

    public function store(Request $request)
    {
        $cart = session(self::SESSION_KEY, []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'Je mandje is leeg.']);
        }

        $isGuest = !Auth::check();

        $validated = $request->validate([
            'guest_name'  => $isGuest ? 'required|string|max:255' : 'nullable|string|max:255',
            'guest_email' => $isGuest ? 'required|email|max:255' : 'nullable|email|max:255',
            'guest_phone' => $isGuest ? 'required|string|max:30' : 'nullable|string|max:30',
        ]);

        $productIds = array_map('intval', array_keys($cart));

        $products = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        foreach ($cart as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            $product = $products->get($productId);

            if (!$product) {
                return redirect()
                    ->route('cart.index')
                    ->withErrors(['stock' => 'Product niet gevonden in winkelmand.']);
            }

            if ($product->stock < $quantity) {
                return redirect()
                    ->route('cart.index')
                    ->withErrors([
                        'stock' => "Niet genoeg voorraad voor {$product->name}.",
                    ]);
            }
        }

        $totalPrice = collect($cart)->sum(function ($quantity, $productId) use ($products) {
            $product = $products->get((int) $productId);

            return $product ? $product->price * (int) $quantity : 0;
        });

        $order = DB::transaction(function () use ($cart, $products, $totalPrice, $isGuest, $validated) {

            $order = Order::create([
                'user_id'     => $isGuest ? null : Auth::id(),
                'guest_name'  => $isGuest ? $validated['guest_name'] : null,
                'guest_email' => $isGuest ? $validated['guest_email'] : null,
                'guest_phone' => $isGuest ? $validated['guest_phone'] : null,
                'date'        => now()->toDateString(),
                'status'      => OrderStatus::InProgress,
                'price'       => $totalPrice,
            ]);

            foreach ($cart as $productId => $quantity) {
                $productId = (int) $productId;
                $quantity = (int) $quantity;

                $order->products()->attach($productId, [
                    'quantity' => $quantity,
                ]);

                $product = $products->get($productId);

                if ($product) {
                    $product->decrement('stock', $quantity);
                }
            }

            return $order;
        });

        session()->forget(self::SESSION_KEY);

        return redirect()
            ->route($isGuest ? 'home' : 'orders.index')
            ->with('success', "Bestelling #{$order->id} geplaatst.");
    }

    public function index()
    {
        if (!Auth::check()) {
            abort(403);
        }

        $orders = Order::with('products')
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function manage()
    {
        $user = Auth::user();

        if (!$user || $user->role !== Role::Owner) {
            abort(403);
        }

        $orders = Order::with(['products', 'user'])
            ->orderByDesc('date')
            ->get();

        return view('orders.manage', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || $user->role !== Role::Owner) {
            abort(403);
        }

        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_map(
                fn ($case) => $case->value,
                OrderStatus::cases()
            )),
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('orders.manage')
            ->with('success', 'Status bijgewerkt.');
    }
}