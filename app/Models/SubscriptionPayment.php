<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'subscription_id', 'razorpay_payment_id', 'amount_paise',
        'status', 'paid_at', 'raw_payload',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'raw_payload' => 'array'];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
