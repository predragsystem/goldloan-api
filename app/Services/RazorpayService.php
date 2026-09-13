<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Razorpay\Api\Api;

/**
 * Thin wrapper around the Razorpay PHP SDK (razorpay/razorpay on Packagist —
 * see composer.json). We use one-time Orders per billing cycle rather than
 * Razorpay's native "Subscription" product, because our plans are simple
 * fixed-duration passes (30/90/365 days) and one-time orders are far less
 * to wire up correctly than recurring mandates. Renewal = the tenant runs
 * checkout again before/after expiry; there is no auto-debit.
 *
 * (If you'd rather have true auto-billing with no manual renewal action,
 * say so — that's Razorpay Subscriptions + a mandate, a bigger integration,
 * and worth a separate discussion before building it.)
 */
class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(config('razorpay.key'), config('razorpay.secret'));
    }

    public function createOrderForPlan(Tenant $tenant, SubscriptionPlan $plan): array
    {
        $order = $this->api->order->create([
            'amount' => $plan->price_paise,
            'currency' => 'INR',
            'receipt' => 'tenant_'.$tenant->id.'_plan_'.$plan->id.'_'.now()->timestamp,
            'notes' => [
                'tenant_id' => (string) $tenant->id,
                'plan_id' => (string) $plan->id,
            ],
        ]);

        SubscriptionPayment::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $this->pendingSubscriptionFor($tenant, $plan)->id,
            'razorpay_payment_id' => null,
            'amount_paise' => $plan->price_paise,
            'status' => 'pending',
            'raw_payload' => ['order_id' => $order['id']],
        ]);

        return [
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key' => config('razorpay.key'),
        ];
    }

    /**
     * Find or create the subscription row this payment is for. Kept
     * separate from activation: the row exists as soon as checkout starts,
     * but only the webhook (source of truth) flips it to "active".
     */
    private function pendingSubscriptionFor(Tenant $tenant, SubscriptionPlan $plan): Subscription
    {
        return Subscription::firstOrCreate(
            ['tenant_id' => $tenant->id, 'plan_id' => $plan->id, 'status' => 'past_due'],
            []
        );
    }

    /**
     * Verify the X-Razorpay-Signature header on an incoming webhook.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            $this->api->utility->verifyWebhookSignature($payload, $signature, config('razorpay.webhook_secret'));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Activate/extend a tenant's subscription after a captured payment.
     * Extends from "now" or from the current period end if renewing early,
     * whichever is later, so early renewal never loses paid-for days.
     */
    public function activateFromPayment(SubscriptionPayment $payment, string $razorpayPaymentId): void
    {
        $subscription = $payment->subscription;
        $plan = $subscription->plan;

        $start = $subscription->current_period_end && $subscription->current_period_end->isFuture()
            ? $subscription->current_period_end
            : now();

        $subscription->update([
            'status' => 'active',
            'razorpay_subscription_id' => $subscription->razorpay_subscription_id ?? $razorpayPaymentId,
            'current_period_start' => $start,
            'current_period_end' => $start->copy()->addDays($plan->duration_days),
        ]);

        $payment->update([
            'razorpay_payment_id' => $razorpayPaymentId,
            'status' => 'captured',
            'paid_at' => now(),
        ]);

        $subscription->tenant->update(['status' => 'active']);
    }

    public function markPaymentFailed(SubscriptionPayment $payment): void
    {
        $payment->update(['status' => 'failed']);
    }
}
