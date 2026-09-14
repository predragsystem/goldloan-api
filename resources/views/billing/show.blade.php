@extends('layouts.app')

@section('title', 'Billing')

@section('content')

@if ($subscription && $subscription->status === 'active' && $subscription->current_period_end->isFuture())
    <div class="border border-success/30 bg-success/10 text-sm rounded-sm p-4 mb-8">
        <span class="font-medium text-success">{{ $subscription->plan->name }} plan active</span>
        <span class="text-ink-soft"> — renews or expires on {{ $subscription->current_period_end->format('d M Y') }}.</span>
    </div>
@else
    <div class="border border-alert/30 bg-alert/5 text-sm rounded-sm p-4 mb-8">
        <span class="font-medium text-alert">No active subscription.</span>
        <span class="text-ink-soft"> Choose a plan below to unlock the dashboard.</span>
    </div>
@endif

<div class="border-t border-paper-line max-w-2xl">
    @foreach ($plans as $plan)
        <div class="flex items-center justify-between py-6 border-b border-paper-line">
            <div>
                <p class="font-display text-xl text-ink">{{ $plan->name }}</p>
                <p class="text-sm text-ink-soft">{{ $plan->duration_days }} days of access</p>
            </div>
            <div class="flex items-center gap-6">
                <p class="font-display text-2xl text-ink tabular">₹{{ number_format($plan->price_paise / 100) }}</p>
                <form method="POST" action="{{ route('billing.checkout') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                        Choose
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

@endsection
