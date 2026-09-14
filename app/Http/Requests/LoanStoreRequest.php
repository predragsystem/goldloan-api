<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'loan_date' => ['required', 'date'],
            'principal_paise' => ['required', 'integer', 'min:100'],
            'interest_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jewellery_type_id' => ['required', 'exists:jewellery_types,id'],
            'items.*.jewellery_quality_id' => ['required', 'exists:jewellery_qualities,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.total_grams' => ['required', 'numeric', 'min:0.0001'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.photo_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
