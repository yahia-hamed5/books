<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * 1. إتمام الطلب (Checkout)
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        // 1. نجيب سلة المستخدم مع محتوياتها
        $cart = auth()->user()->cart()->with('cartItems.product')->first();
        // return response()->json([
        //     'cart' => $cart
        // ]);.01
        // // 2. فحص: هل السلة فاضية؟
        if(!$cart || $cart->cartItems->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cart is empty. Please add items before checkout.',
            ], 400);
        }

        // // 3. فحص المخزون لآخر مرة قبل أي عملية
        foreach ($cart->cartItems as $item) {
            $product = $item->product;

            if (!$product || !$product->is_active) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Product '{$product?->name}' is no longer available.",
                ], 422);
            }

            if ($item->quantity > $product->qty) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Product '{$product->name}' only has {$product->qty} items in stock. You requested {$item->quantity}.",
                ], 422);
            }
        }

        // // 4. حساب الإجمالي الكلي للفلوس
        // $totalPrice = $cart->cartItems->sum(function ($item) {
        //     return $item->product->price * $item->quantity;
        // });

        // // 5. توليد كود مميز للطلب زي: ORD-A1B2C3D4
        // $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // // 6. استخدام الـ Transaction (سر الأمان)
        // // لو أي خطوة جوه فشلت، كل حاجة بترجع زي ما كانت أوتوماتيك
        // $order = DB::transaction(function () use ($request, $cart, $totalPrice, $orderNumber) {

        //     // أ) إنشاء الفاتورة الرئيسية
        //     $order = Order::create([
        //         'user_id'          => auth()->id(),
        //         'order_number'     => $orderNumber,
        //         'status'           => 'pending',
        //         'total_price'      => $totalPrice,
        //         'shipping_address' => $request->shipping_address,
        //         'phone'            => $request->phone,
        //         'notes'            => $request->notes,
        //         'payment_method'   => $request->input('payment_method', 'cash_on_delivery'),
        //         'payment_status'   => 'unpaid',
        //     ]);

        //     // ب) نقل كل كتاب من السلة إلى الفاتورة + الخصم من المخزن
        //     foreach ($cart->cartItems as $item) {
        //         $unitPrice = (float) $item->product->price;
        //         $subtotal  = $unitPrice * $item->quantity;

        //         // إنشاء سطر في جدول order_items
        //         $order->orderItems()->create([
        //             'product_id' => $item->product_id,
        //             'quantity'   => $item->quantity,
        //             'unit_price' => $unitPrice,
        //             'subtotal'   => $subtotal,
        //         ]);

        //         // ⚡️ خصم الكمية من المخزن رسمياً!
        //         $item->product->decrement('qty', $item->quantity);
        //     }

        //     // ج) تفريغ السلة خلاص لأنها اتحولت لطلب رسمي
        //     $cart->cartItems()->delete();

        //     return $order;
        // });

        // // نرجع تفاصيل الطلب كاملة مع الكتب للعميل
        // $order->load('orderItems.product');

        // return response()->json([
        //     'status'  => 'success',
        //     'message' => 'Order placed successfully',
        //     'order'   => $order,
        // ], 201);
    }
}
