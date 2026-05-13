<?php

namespace App\Http\Requests\Authorization;

use App\Http\Responses\ApiResponse;
use App\Models\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException((new ApiResponse)->validator($validator)->json());
    }

    public function after(): array
    {
        return [
            function ($validator) {
                if (empty($this->all())) {
                    $validator->errors()->add(
                        'request',
                        'Envie ao menos um campo.'
                    );
                }
            },
        ];
    }

    public function rules(): array
    {
        $rules = [
            'roles' => ['required', 'array'],
            'roles.*' => ['integer', Rule::exists(Role::class, 'id')->where('guard_name', 'api')],
        ];

        return $rules;
    }
}
