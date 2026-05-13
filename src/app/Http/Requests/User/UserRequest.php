<?php

namespace App\Http\Requests\User;

use App\Http\Responses\ApiResponse;
use App\Models\PersonProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'active' => ['sometimes', 'boolean'],
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'email' => ['required', 'string', 'max:255', 'email', Rule::unique(User::class, 'email')->ignore($this->route('user'))],
                'person_profile_id' => ['required', 'integer', Rule::exists(PersonProfile::class, 'id'), Rule::unique(User::class, 'person_profile_id')],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['integer', 'distinct', Rule::exists(Role::class, 'id')],
                'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules += [
                'email' => ['sometimes', 'string', 'max:255', 'email', Rule::unique(User::class, 'email')->ignore($this->route('user'))],
                'roles' => ['sometimes', 'array', 'min:1'],
                'roles.*' => ['integer', 'distinct', Rule::exists(Role::class, 'id')],
                'password' => ['sometimes', 'nullable', 'string',  'min:8', 'max:255', 'confirmed'],
            ];
        }

        return $rules;
    }
}
