<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Monthly', 'slug' => 'monthly', 'duration_days' => 30, 'price_paise' => 19900],
            ['name' => '6 Month', 'slug' => 'half-yearly', 'duration_days' => 182, 'price_paise' => 109900],
            ['name' => 'Yearly', 'slug' => 'yearly', 'duration_days' => 365, 'price_paise' => 219900],
        ];

        foreach ($plans as $plan) {
            // updateOrCreate (not firstOrCreate) so re-running the seeder after a
            // price change actually updates existing rows instead of ignoring them.
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
