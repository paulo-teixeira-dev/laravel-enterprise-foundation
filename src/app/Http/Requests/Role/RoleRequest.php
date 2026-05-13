<?php

namespace App\Http\Requests\Role;

use App\Http\Responses\ApiResponse;
use App\Models\Permission;
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'permissions' => ['required', 'array', 'min:1'],
                'permissions.*' => ['integer', 'distinct', Rule::exists(Permission::class, 'id')],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules += [
                'permissions' => ['sometimes', 'array', 'min:1'],
                'permissions.*' => ['integer', 'distinct',  Rule::exists(Permission::class, 'id')],
            ];
        }

        return $rules;
    }
}
