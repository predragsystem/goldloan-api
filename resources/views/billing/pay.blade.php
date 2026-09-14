@extends('layouts.app')

@section('title', 'Complete payment')

@section('content')

<div class="max-w-md border border-paper-line bg-white p-6">
    <h2 class="font-display text-lg text-ink">Pay for {{ $plan->name }}</h2>
    <p class="mt-1 text-ink-soft text-sm">₹{{ number_format($plan->price_paise / 100) }} · {{ $plan->duration_days }} days</p>

    <button id="pay-button" class="mt-6 w-full bg-ink text-paper py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
        Pay now
    </button>
    <a href="{{ route('billing.show') }}" class="mt-3 block text-center text-sm text-ink-soft hover:text-ink">Cancel</a>

    <p class="mt-6 text-xs text-ink-soft">
        Your plan activates automatically once the payment is confirmed — this can take a few seconds
        after you complete it. Refresh the billing page if it doesn't reflect right away.
    </p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        const options = {
            key: '{{ $order['key'] }}',
            amount: '{{ $order['amount'] }}',
            currency: '{{ $order['currency'] }}',
            order_id: '{{ $order['order_id'] }}',
            name: 'GoldLoan',
            description: '{{ $plan->name }} plan',
            handler: function () {
                window.location.href = '{{ route('billing.show') }}';
            },
            theme: { color: '#1E2A22' },
        };
        new Razorpay(options).open();
    });
</script>

@endsection
