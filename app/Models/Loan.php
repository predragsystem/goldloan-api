<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'loan_no', 'customer_id', 'loan_date', 'principal_paise',
        'interest_rate_percent', 'status', 'outstanding_balance_paise', 'created_by',
    ];

    protected function casts(): array
    {
        return ['loan_date' => 'date'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
