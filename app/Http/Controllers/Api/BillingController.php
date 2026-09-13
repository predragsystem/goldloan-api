<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct(private RazorpayService $razorpay) {}

    public function plans()
    {
        return SubscriptionPlan::where('is_active', true)->get([
            'id', 'name', 'slug', 'duration_days', 'price_paise',
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $tenant = $request->user()->tenant;

        $order = $this->razorpay->createOrderForPlan($tenant, $plan);

        return response()->json($order);
    }

    public function subscription(Request $request)
    {
        $subscription = $request->user()->tenant->activeSubscription;

        if (! $subscription) {
            return response()->json(['status' => 'none']);
        }

        return response()->json([
            'plan' => $subscription->plan->name,
            'status' => $subscription->status,
            'current_period_end' => $subscription->current_period_end,
            'is_active' => $request->user()->tenant->isSubscriptionActive(),
        ]);
    }

    public function history(Request $request)
    {
        return $request->user()->tenant
            ->subscriptions()
            ->with('payments')
            ->get()
            ->pluck('payments')
            ->flatten();
    }

    /**
     * Razorpay server-to-server webhook. This — not the client's checkout
     * call — is the only source of truth for "did the payment succeed",
     * since a client can crash/close the app after paying but before
     * telling our API. Configure this URL in the Razorpay dashboard under
     * Settings > Webhooks, subscribed to payment.captured and payment.failed.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature', '');

        if (! $this->razorpay->verifyWebhookSignature($request->getContent(), $signature)) {
            Log::warning('Razorpay webhook signature verification failed.');

            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $event = $request->input('event');
        $paymentEntity = $request->input('payload.payment.entity', []);
        $orderId = $paymentEntity['order_id'] ?? null;

        $payment = SubscriptionPayment::where('raw_payload->order_id', $orderId)->first();

        if (! $payment) {
            Log::warning('Razorpay webhook: no matching subscription_payment for order.', ['order_id' => $orderId]);

            return response()->json(['message' => 'Ignored — unknown order.']);
        }

        match ($event) {
            'payment.captured' => $this->razorpay->activateFromPayment($payment, $paymentEntity['id']),
            'payment.failed' => $this->razorpay->markPaymentFailed($payment),
            default => Log::info('Unhandled Razorpay event.', ['event' => $event]),
        };

        return response()->json(['message' => 'ok']);
    }
}
