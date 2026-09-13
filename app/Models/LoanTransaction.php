<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'type', 'amount_paise', 'principal_component_paise',
        'interest_component_paise', 'balance_after_paise', 'transaction_date', 'created_by',
    ];

    protected function casts(): array
    {
        return ['transaction_date' => 'date'];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
