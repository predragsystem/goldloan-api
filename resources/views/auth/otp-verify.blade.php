@extends('layouts.guest')

@section('title', 'Verify your phone')

@section('content')
<section class="mx-auto max-w-md px-6 py-20">
    <h1 class="font-display text-3xl font-semibold text-ink">Verify your phone</h1>
    <p class="mt-2 text-ink-soft">
        Enter the 6-digit code sent to <span class="text-ink font-medium">{{ $phone }}</span>.
    </p>

    @if ($errors->any())
        <div class="mt-6 border border-alert/30 bg-alert/5 text-alert text-sm rounded-sm p-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">
        <input type="hidden" name="purpose" value="{{ $purpose }}">
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Verification code</label>
            <input type="text" name="otp" required maxlength="6" inputmode="numeric" autofocus
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white text-2xl tracking-[0.3em] tabular focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <button type="submit"
                class="w-full bg-ink text-paper py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
            Verify
        </button>
    </form>

    <form method="POST" action="{{ route('otp.request') }}" class="mt-4">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">
        <input type="hidden" name="purpose" value="{{ $purpose }}">
        <button type="submit" class="text-sm text-ink-soft hover:text-ink underline">Resend code</button>
    </form>
</section>
@endsection
