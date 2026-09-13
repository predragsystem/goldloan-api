<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'jewellery_type_id', 'jewellery_quality_id', 'quantity',
        'total_grams', 'description', 'photo_url',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function jewelleryType(): BelongsTo
    {
        return $this->belongsTo(JewelleryType::class);
    }

    public function jewelleryQuality(): BelongsTo
    {
        return $this->belongsTo(JewelleryQuality::class);
    }
}
