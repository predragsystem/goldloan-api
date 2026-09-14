@extends('layouts.guest')

@section('title', 'Create your account')

@section('content')
<section class="mx-auto max-w-md px-6 py-20">
    <h1 class="font-display text-3xl font-semibold text-ink">Create your account</h1>
    <p class="mt-2 text-ink-soft">Set up your lending business on GoldLoan.</p>

    @if ($errors->any())
        <div class="mt-6 border border-alert/30 bg-alert/5 text-alert text-sm rounded-sm p-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('signup.store') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Business name</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Your name</label>
            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Phone number</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Email (optional)</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <button type="submit"
                class="w-full bg-ink text-paper py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
            Continue
        </button>
    </form>

    <p class="mt-6 text-sm text-ink-soft">
        Already have an account? <a href="{{ route('login') }}" class="text-ink underline">Log in</a>
    </p>
</section>
@endsection
