<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart()->with('cartItems.product')->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cart is empty',
                'data' => [
                    'items' => [],
                    'total_items' => 0,
                    'grand_total' => 0,
                ],
            ], 200);
        }
        $totalItems = 0;
        $grandTotal = 0;
        $items = $cart->cartItems->map(function ($item) use (&$totalItems, &$grandTotal) {
            $unitPrice = (float) $item->product->price;
            $subtotal = $unitPrice * $item->quantity;
            $totalItems += $item->quantity;
            $grandTotal += $subtotal;
            return [
                'cart_item_id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'image' => $item->product->image,
                'unit_price' => $unitPrice,
                'quantity' => $item->quantity,
                'subtotal' => round($subtotal, 2),
                'available_stock' => $item->product->qty,
            ];
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Cart retrieved successfully',
            'data' => [
                'cart_id' => $cart->id,
                'items' => $items,
                'total_items' => $totalItems,
                'grand_total' => round($grandTotal, 2),
            ],
        ], 200);
    }
    public function store(AddToCartRequest $request)
    {

        // هنا بنجيب منتج id ال داخل  وكمان لو هوا باعت الكميه بياخدها لو مفيش هيبق ديفولت ب واحد
        $productId = $request->product_id;
        $quantity = $request->input('quantity', 1);

        // هنا هنجيب الكتاب ونتاكد انه موجود ونفس الوقت active
        $product = Product::find($productId);
        if (!$product || !$product->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found or inactive',
            ], 404);
        }

        // هنا هنتاكد الكميه دي مش هتتعدى اللي موجود
        if ($quantity > $product->qty) {
            return response()->json([
                'status' => 'error',
                'message' => "Product '{$product->name}' only has {$product->qty} items in stock. You requested {$quantity}.",
            ], 400);
        }

        // هنجيب السله لو لسا معندوش هنعمله
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // هنشوف ان الكتاب موجود ف الكارت ولا لا
        $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
        // نعدل الكميه بقا
        $newQty = $cartItem ? ($cartItem->quantity + $quantity) : $quantity;
        if ($newQty > $product->qty) {
            return response()->json([
                'status' => 'error',
                'message' => "Requested quantity exceeds available stock. Only {$product->qty} items available.",
            ], 422);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
        ], 201);
    }

    public function update(UpdateCartItemRequest $request, string $id)
    {
        $cart = auth()->user()->cart;

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found',
            ], 404);
        }

        $cartItem = $cart->cartItems()->where('id', $id)->with('product')->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found in your cart',
            ], 404);
        }

        $newQty = $request->quantity;

        if ($newQty > $cartItem->product->qty) {
            return response()->json([
                'status' => 'error',
                'message' => "Requested quantity exceeds available stock. Only {$cartItem->product->qty} items available.",
            ], 422);
        }

        $cartItem->update(['quantity' => $newQty]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated successfully',
        ], 200);
    }

    public function destroy(string $id)
    {
        $cart = auth()->user()->cart;

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found',
            ], 404);
        }

        $cartItem = $cart->cartItems()->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found in your cart',
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart successfully',
        ], 200);
    }

    public function clear()
    {
        $cart = auth()->user()->cart;

        if ($cart) {
            $cart->cartItems()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared successfully',
        ], 200);
    }
}
