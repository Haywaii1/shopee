<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    // Add item to cart
    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);

        $item = [
            "id" => $request->id,
            "name" => $request->name,
            "price" => $request->price,
            "quantity" => $request->quantity
        ];

        $cart[$item['id']] = $item;
        session()->put('cart', $cart);

        return response()->json(['message' => 'Item added to cart', 'cart' => $cart], 200);
    }

    // View cart
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return response()->json(['cart' => $cart], 200);
    }

    // Update cart item
    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return response()->json(['message' => 'Cart updated', 'cart' => $cart], 200);
        }

        return response()->json(['message' => 'Item not found in cart'], 404);
    }

    // Remove item from cart
    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);

            return response()->json(['message' => 'Item removed from cart', 'cart' => $cart], 200);
        }

        return response()->json(['message' => 'Item not found in cart'], 404);
    }
}
