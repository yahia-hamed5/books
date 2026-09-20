<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'=>'required|email|exists:users,email',
            'password'=>'required|min:6|max:20'
        ];
    }

    public function messages(): array{
        return [
            'email.required'=>"email is required",
            'email.email'=>"email is not valid",
            'email.exists'=>"email is not exists",
            'password.required'=>"password is required",
            'password.min'=>"password is too short",
            'password.max'=>"password is too long"
        ];
    }
}
