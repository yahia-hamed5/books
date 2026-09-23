<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // العنوان ورقم التليفون إجباري
            'shipping_address' => 'required|string|max:500',
            'phone'            => 'required|string|max:20',

            // ملاحظات اختيارية للمندوب
            'notes'            => 'nullable|string|max:1000',

            // طريقة الدفع (إما كاش أو كارت)
            'payment_method'   => 'nullable|string|in:cash_on_delivery,card',
        ];
    }
}
