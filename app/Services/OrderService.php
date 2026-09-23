<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * إنشاء طلب من محتويات السلة
     */
    public function createOrderFromCart($user, array $data): Order
    {
        // 1. جلب سلة المستخدم مع محتوياتها
        $cart = $user->cart()->with('cartItems.product')->first();

        // 2. التأكد إن السلة مش فاضية
        if (!$cart || $cart->cartItems->isEmpty()) {
            throw new Exception('Cart is empty. Please add items before checkout.');
        }

        // 3. فحص المخزون لكل كتاب
        foreach ($cart->cartItems as $item) {
            $product = $item->product;

            if (!$product || !$product->is_active) {
                throw new Exception("Product '{$product?->name}' is no longer available.");
            }

            if ($item->quantity > $product->qty) {
                throw new Exception("Product '{$product->name}' only has {$product->qty} items in stock. You requested {$item->quantity}.");
            }
        }

        // 4. حساب الإجمالي الكلي
        $totalPrice = $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // 5. توليد كود الطلب
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // 6. تنفيذ العملية داخل Database Transaction
        return DB::transaction(function () use ($user, $cart, $data, $totalPrice, $orderNumber) {
            // أ) إنشاء الفاتورة
            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => $orderNumber,
                'status'           => 'pending',
                'total_price'      => $totalPrice,
                'shipping_address' => $data['shipping_address'],
                'phone'            => $data['phone'],
                'notes'            => $data['notes'] ?? null,
                'payment_method'   => $data['payment_method'] ?? 'cash_on_delivery',
                'payment_status'   => 'unpaid',
            ]);

            // ب) نقل الكتب للفاتورة وخصم الكميات من المخزن
            foreach ($cart->cartItems as $item) {
                $unitPrice = (float) $item->product->price;
                $subtotal  = $unitPrice * $item->quantity;

                $order->orderItems()->create([
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ]);

                // خصم من المخزن
                $item->product->decrement('qty', $item->quantity);
            }

            // ج) تفريغ السلة
            $cart->cartItems()->delete();

            // تحميل الكتب مع الطلب
            $order->load('orderItems.product');

            return $order;
        });
    }
}
