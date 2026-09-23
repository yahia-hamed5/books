<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'bref' => 'sometimes|required|string',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'qty' => 'sometimes|required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'author_id' => 'sometimes|required|uuid|exists:authors,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'uuid|exists:brands,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'uuid|exists:categories,id',
        ];
    }
}
