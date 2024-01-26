<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOrRemoveFromCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Return User Cart
     */
    public function myCart(Request $request)
    {
        $cart = Cart::userCartOrCreate($request->user()->id);
        return new CartResource($cart);
    }

    /**
     * Add to cart
     */
    public function addToCart(AddOrRemoveFromCartRequest $request)
    {
        $cart = Cart::userCartOrCreate($request->user()->id);
        $cart->attachProducts($request->products);

        return response()->json([], 200);
    }

    /**
     * Remove From cart
     */
    public function removeFromCart(AddOrRemoveFromCartRequest $request)
    {
        $cart = Cart::userCartOrCreate($request->user()->id);

        $cart->products()->detach($request->products);

        return response()->json([], 200);
    }

    /**
     * Set the cart as the given products
     */
    public function setCart(AddOrRemoveFromCartRequest $request)
    {
        $cart = Cart::userCartOrCreate($request->user()->id);

        // Detach all old products
        $cart->products()->detach($cart->products->pluck('id')->toArray());

        // Attach given products to the list
        $cart->attachProducts($request->products);
        return response()->json([], 200);
    }
}
