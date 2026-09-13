<?php

return [
    /*
     * Days after current_period_end during which access is still allowed,
     * to absorb a delayed/retried Razorpay auto-charge before hard-locking
     * the tenant out. Set to 0 for an immediate lock on expiry.
     */
    'grace_days' => env('SUBSCRIPTION_GRACE_DAYS', 3),
];
