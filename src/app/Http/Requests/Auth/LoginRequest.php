<?php

namespace App\Http\Requests\Auth;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException((new ApiResponse)->validator($validator)->json());
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'max:255', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];

        return $rules;
    }
}
