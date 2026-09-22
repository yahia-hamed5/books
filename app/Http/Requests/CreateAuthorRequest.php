<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string",
            "image" => "nullable|string",
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "Name is required",
            "name.string" => "Name must be a string",
            "image.string" => "Image must be a string",
        ];
    }
}
