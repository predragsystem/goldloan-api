<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_paise' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }
}
