<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(private RazorpayService $razorpay) {}

    public function show(Request $request)
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $subscription = $request->user()->tenant->activeSubscription;

        return view('billing.show', compact('plans', 'subscription'));
    }

    public function checkout(CheckoutRequest $request)
    {
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $order = $this->razorpay->createOrderForPlan($request->user()->tenant, $plan);

        return view('billing.pay', compact('order', 'plan'));
    }
}
