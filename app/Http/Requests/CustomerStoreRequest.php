<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:15'],
            'address' => ['nullable', 'string', 'max:500'],
            'aadhar_no' => ['nullable', 'string', 'max:15'],
            'photo_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
