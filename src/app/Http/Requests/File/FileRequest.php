<?php

namespace App\Http\Requests\File;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:jpg,png,pdf,docx', 'max:2048'],
        ];

        return $rules;
    }
}
