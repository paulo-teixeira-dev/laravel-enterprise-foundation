<?php

namespace App\Http\Requests\Permission;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionRequest extends FormRequest
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
            'permissions' => ['required', 'array'],
            'permissions.*' => ['integer', Rule::exists(Permission::class, 'id')->where('guard_name', 'api')],
        ];

        return $rules;
    }
}
