<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bref' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'author_id' => 'required|uuid|exists:authors,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'uuid|exists:brands,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'uuid|exists:categories,id',
        ];
    }
}
