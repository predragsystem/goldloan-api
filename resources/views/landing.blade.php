@extends('layouts.guest')

@section('title', 'GoldLoan')

@section('content')

{{-- HERO: left-aligned copy, right-aligned pledge-ticket visual --}}
<section class="mx-auto max-w-6xl px-6 pt-16 pb-20 grid lg:grid-cols-2 gap-16 items-center">
    <div>
        <h1 class="font-display text-4xl sm:text-5xl font-semibold leading-[1.1] text-ink">
            Every pledge, every rupee,<br>accounted for.
        </h1>
        <p class="mt-6 text-lg text-ink-soft max-w-[46ch]">
            GoldLoan replaces the register book with a proper system for gold-loan
            and pawnbroking lenders — one place to log a pledge, collect interest,
            and know exactly what's outstanding.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-4">
            <a href="{{ route('signup') }}"
               class="inline-flex items-center gap-2 bg-ink text-paper px-6 py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
                Get started
            </a>
            <a href="#pricing" class="text-ink-soft hover:text-ink text-sm font-medium">
                See pricing
            </a>
        </div>
    </div>

    {{-- The one bold visual moment: a stylised pledge ticket, not a screenshot or gradient blob --}}
    <div class="relative">
        <div class="border border-paper-line bg-white rounded-sm shadow-sm p-6 rotate-1">
            <div class="flex items-start justify-between border-b border-paper-line pb-3 mb-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-ink-soft">Loan No.</p>
                    <p class="font-display text-2xl text-ink tabular">0187</p>
                </div>
                <span class="text-xs font-medium text-success border border-success/30 bg-success/10 px-2 py-1 rounded-sm">
                    Active
                </span>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-ink-soft">Customer</dt>
                    <dd class="text-ink font-medium">Ramesh Kulkarni</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink-soft">Pledged</dt>
                    <dd class="text-ink tabular">22 KT Necklace, 41.2g</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink-soft">Principal</dt>
                    <dd class="text-ink font-medium tabular">₹42,000</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink-soft">Rate</dt>
                    <dd class="text-ink tabular">2.0% / month</dd>
                </div>
            </dl>
        </div>
        <div class="absolute -bottom-4 -right-4 border-2 border-brass text-brass font-display font-semibold text-sm px-3 py-1 rotate-[-8deg] rounded-sm bg-paper">
            GIRVI
        </div>
    </div>
</section>

<div class="border-t border-paper-line"></div>

{{-- FEATURES: varied widths, not identical cards --}}
<section class="mx-auto max-w-6xl px-6 py-20">
    <h2 class="font-display text-2xl font-semibold text-ink max-w-[30ch]">
        Built for how a lending counter actually runs
    </h2>

    <div class="mt-12 grid md:grid-cols-3 gap-10">
        <div class="md:col-span-2">
            <h3 class="font-medium text-ink text-lg">One ledger, not three notebooks</h3>
            <p class="mt-2 text-ink-soft max-w-[52ch]">
                Every pledge, payment, top-up, and closure lands in a single running
                transaction history per loan — searchable by customer, date, or status,
                so nothing depends on remembering which register a payment went into.
            </p>
        </div>
        <div>
            <h3 class="font-medium text-ink text-lg">Interest, your way</h3>
            <p class="mt-2 text-ink-soft">
                Choose flat-monthly or daily-proportional interest for your business —
                calculated the same way every time, automatically.
            </p>
        </div>
        <div>
            <h3 class="font-medium text-ink text-lg">Your staff, their own logins</h3>
            <p class="mt-2 text-ink-soft">
                Give cashiers their own accounts without handing over billing or
                staff management.
            </p>
        </div>
        <div class="md:col-span-2">
            <h3 class="font-medium text-ink text-lg">Works from the counter or your pocket</h3>
            <p class="mt-2 text-ink-soft max-w-[52ch]">
                Log a loan from the shop computer, then check today's collections from
                your phone on the way home — same data, either way.
            </p>
        </div>
    </div>
</section>

<div class="border-t border-paper-line"></div>

{{-- PRICING: divided rows, not identical shadow cards --}}
<section id="pricing" class="mx-auto max-w-6xl px-6 py-20">
    <h2 class="font-display text-2xl font-semibold text-ink">Pricing</h2>
    <p class="mt-2 text-ink-soft">Pay for what you use. No setup fee, cancel anytime.</p>

    <div class="mt-10 border-t border-paper-line">
        @foreach ($plans as $plan)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-6 border-b border-paper-line">
                <div>
                    <p class="font-display text-xl text-ink">{{ $plan->name }}</p>
                    <p class="text-sm text-ink-soft">{{ $plan->duration_days }} days of access</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-6">
                    <p class="font-display text-2xl text-ink tabular">
                        ₹{{ number_format($plan->price_paise / 100) }}
                    </p>
                    <a href="{{ route('signup') }}"
                       class="text-sm font-medium text-paper bg-ink px-4 py-2 rounded-sm hover:bg-brass-dark transition-colors">
                        Choose {{ $plan->name }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>

<div class="border-t border-paper-line"></div>

{{-- STEPS: genuinely sequential, numbering is earned here --}}
<section class="mx-auto max-w-6xl px-6 py-20">
    <h2 class="font-display text-2xl font-semibold text-ink">Get started in a few minutes</h2>

    <ol class="mt-10 grid sm:grid-cols-3 gap-10">
        <li>
            <p class="font-display text-3xl text-brass">1</p>
            <h3 class="mt-2 font-medium text-ink">Create your account</h3>
            <p class="mt-1 text-ink-soft text-sm">Tell us your business name and phone number.</p>
        </li>
        <li>
            <p class="font-display text-3xl text-brass">2</p>
            <h3 class="mt-2 font-medium text-ink">Verify your phone</h3>
            <p class="mt-1 text-ink-soft text-sm">A one-time code confirms it's really you.</p>
        </li>
        <li>
            <p class="font-display text-3xl text-brass">3</p>
            <h3 class="mt-2 font-medium text-ink">Start logging loans</h3>
            <p class="mt-1 text-ink-soft text-sm">Pick a plan and you're straight into the dashboard.</p>
        </li>
    </ol>

    <a href="{{ route('signup') }}"
       class="mt-12 inline-flex items-center gap-2 bg-ink text-paper px-6 py-3 rounded-sm font-medium hover:bg-brass-dark transition-colors">
        Get started
    </a>
</section>

@endsection
