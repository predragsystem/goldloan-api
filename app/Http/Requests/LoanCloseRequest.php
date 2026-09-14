<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanCloseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // The payoff amount is server-computed (principal + accrued interest);
            // this is only for recording how it was physically collected.
            'transaction_date' => ['nullable', 'date'],
        ];
    }
}
