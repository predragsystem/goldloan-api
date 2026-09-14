@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
<section class="mx-auto max-w-md px-6 py-20">
    <h1 class="font-display text-3xl font-semibold text-ink">Log in</h1>

    @if ($errors->any())
        <div class="mt-6 border border-alert/30 bg-alert/5 text-alert text-sm rounded-sm p-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Phone or email</label>
            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <button type="submit"
                class="w-full bg-ink text-paper py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
            Log in
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-paper-line">
        <form method="POST" action="{{ route('otp.request') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="purpose" value="login">
            <label class="block text-sm font-medium text-ink mb-1">Or log in with a phone code</label>
            <div class="flex gap-2">
                <input type="tel" name="phone" placeholder="Phone number" required
                       class="flex-1 border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
                <button type="submit" class="border border-ink text-ink px-4 py-2 rounded-sm text-sm font-medium hover:bg-ink hover:text-paper transition-colors">
                    Send code
                </button>
            </div>
        </form>
    </div>

    <p class="mt-6 text-sm text-ink-soft">
        New here? <a href="{{ route('signup') }}" class="text-ink underline">Create an account</a>
    </p>
</section>
@endsection
