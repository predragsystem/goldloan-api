<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Placeholder prices — update price_paise once real pricing is decided,
     * and fill in razorpay_plan_id after creating the matching plans in the
     * Razorpay dashboard/API.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Monthly', 'slug' => 'monthly', 'duration_days' => 30, 'price_paise' => 99900],
            ['name' => 'Quarterly', 'slug' => 'quarterly', 'duration_days' => 90, 'price_paise' => 269900],
            ['name' => 'Yearly', 'slug' => 'yearly', 'duration_days' => 365, 'price_paise' => 999900],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
