<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDocument extends Model
{
    use HasFactory;

    protected $fillable = ['loan_id', 'type', 'url'];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
