<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'duration_days', 'price_paise', 'razorpay_plan_id', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
