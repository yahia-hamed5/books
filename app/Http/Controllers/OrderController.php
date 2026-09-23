<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * إتمام الطلب (Checkout)
     */
    public function checkout(CheckoutRequest $request, OrderService $orderService): JsonResponse
    {
        try {
            // بننادي على السيرفيس ونبعت لها اليوزر والداتا اللي اتعملها فاليديشن
            $order = $orderService->createOrderFromCart(auth()->user(), $request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Order placed successfully',
                'order'   => $order,
            ], 201);

        } catch (Exception $e) {
            // لو السلة كانت فاضية أو المخزن ميكفيش، السيرفيس هترمي Exception وهيتم اصطياده هنا فوراً
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
