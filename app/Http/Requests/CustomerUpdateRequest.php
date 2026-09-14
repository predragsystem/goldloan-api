<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = $this->user()->tenant_id;
        $customerId = $this->route('customer')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'phone' => [
                'required', 'string', 'max:15',
                Rule::unique('customers')->where('tenant_id', $tenantId)->ignore($customerId),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'aadhar_no' => [
                'nullable', 'string', 'max:15',
                Rule::unique('customers')->where('tenant_id', $tenantId)->ignore($customerId),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'A customer with this phone number already exists.',
            'aadhar_no.unique' => 'A customer with this Aadhar number already exists.',
        ];
    }
}
