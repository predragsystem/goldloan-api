<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks every loan-domain endpoint once a tenant's subscription has lapsed
 * (past its grace period). Billing and auth routes are registered outside
 * this middleware group so a locked-out owner can always still pay to
 * unlock — see routes/api.php.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->user()->tenant;
        $subscription = $tenant->activeSubscription;

        $graceDeadline = $subscription?->current_period_end
            ?->copy()->addDays((int) config('subscription.grace_days'));

        $allowed = $subscription && $graceDeadline && $graceDeadline->isFuture();

        if (! $allowed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your subscription has expired. Please renew to continue.',
                    'code' => 'SUBSCRIPTION_EXPIRED',
                ], 403);
            }

            return redirect()
                ->route('billing.show')
                ->with('status', 'Your subscription has expired. Please renew to continue.');
        }

        return $next($request);
    }
}