<?php

namespace App\Http\Requests\Person;

use App\Http\Responses\ApiResponse;
use App\Models\State;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PersonRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'birth_date' => ['required', 'date'],
                'gender' => ['required', 'string', 'in:m,f,n'],
                'nationality' => ['required', 'string', 'max:100'],
                'address' => ['required', 'string', 'max:255'],
                'number' => ['required', 'numeric'],
                'complement' => ['nullable', 'string', 'max:255'],
                'neighborhood' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'state_id' => ['required', 'integer', Rule::exists(State::class, 'id')],
                'postal_code' => ['required', 'string', 'max:50'],
                'person_contact' => ['required', 'array'],
                'person_contact.*.phone' => ['required', 'string', 'max:11'],
                'person_contact.*.email' => ['required', 'email', 'max:255'],
                'person_contact.*.type' => ['required', 'string', 'in:per,com,eme'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'first_name' => ['sometimes', 'filled', 'string', 'max:255'],
                'last_name' => ['sometimes', 'filled', 'string', 'max:255'],
                'birth_date' => ['sometimes', 'filled', 'date'],
                'gender' => ['sometimes', 'filled', 'string', 'in:m,f,n'],
                'nationality' => ['sometimes', 'filled', 'string', 'max:100'],
                'address' => ['sometimes', 'filled', 'string', 'max:255'],
                'number' => ['sometimes', 'filled', 'numeric'],
                'complement' => ['sometimes', 'filled', 'string', 'max:255'],
                'neighborhood' => ['sometimes', 'filled', 'string', 'max:255'],
                'city' => ['sometimes', 'filled', 'string', 'max:255'],
                'state_id' => ['sometimes', 'filled', 'integer', Rule::exists(State::class, 'id')],
                'postal_code' => ['sometimes', 'filled', 'string', 'max:50'],
                'person_contact' => ['sometimes', 'filled', 'array'],
                'person_contact.*.phone' => ['required', 'string', 'max:11'],
                'person_contact.*.email' => ['required', 'email', 'max:255'],
                'person_contact.*.type' => ['required', 'string', 'in:per,com,eme'],
            ];
        }

        return $rules;
    }
}
