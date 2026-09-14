@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-paper-line border border-paper-line">
    <div class="bg-white p-6">
        <p class="text-sm text-ink-soft">Active loans</p>
        <p class="mt-1 font-display text-3xl text-ink tabular">{{ $summary['active_loan_count'] }}</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-sm text-ink-soft">Outstanding principal</p>
        <p class="mt-1 font-display text-3xl text-ink tabular">₹{{ number_format($summary['outstanding_paise'] / 100) }}</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-sm text-ink-soft">Collected this month</p>
        <p class="mt-1 font-display text-3xl text-ink tabular">₹{{ number_format($summary['collected_this_month_paise'] / 100) }}</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-sm text-ink-soft">Customers</p>
        <p class="mt-1 font-display text-3xl text-ink tabular">{{ $summary['customer_count'] }}</p>
    </div>
</div>

<div class="mt-10 flex items-center justify-between">
    <h2 class="font-display text-lg font-semibold text-ink">Recent loans</h2>
    <a href="{{ route('loans.create') }}" class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
        New loan
    </a>
</div>

<div class="mt-4 border border-paper-line bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-paper-line text-left text-ink-soft">
                <th class="px-4 py-3 font-medium">Loan No.</th>
                <th class="px-4 py-3 font-medium">Customer</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium text-right">Principal</th>
                <th class="px-4 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentLoans as $loan)
                <tr class="border-b border-paper-line last:border-0 hover:bg-paper/50">
                    <td class="px-4 py-3">
                        <a href="{{ route('loans.show', $loan) }}" class="text-ink font-medium hover:underline tabular">#{{ $loan->loan_no }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $loan->customer->name }}</td>
                    <td class="px-4 py-3 text-ink-soft">{{ $loan->loan_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right tabular">₹{{ number_format($loan->principal_paise / 100) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-medium px-2 py-1 rounded-sm
                            {{ $loan->status === 'active' ? 'text-success bg-success/10' : 'text-ink-soft bg-paper' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-ink-soft">
                        No loans yet. <a href="{{ route('loans.create') }}" class="text-ink underline">Create the first one</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
