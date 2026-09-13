<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name', 'owner_name', 'phone', 'email', 'status', 'trial_ends_at',
    ];

    protected function casts(): array
    {
        return ['trial_ends_at' => 'datetime'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function isSubscriptionActive(): bool
    {
        $sub = $this->activeSubscription;

        return $sub && $sub->current_period_end && $sub->current_period_end->isFuture();
    }
}
