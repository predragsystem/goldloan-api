@extends('layouts.app')

@section('title', 'Loans')

@section('content')

<div class="flex items-center justify-between gap-4">
    <form method="GET" class="flex gap-2">
        <select name="status" onchange="this.form.submit()"
                class="border border-paper-line rounded-sm px-3 py-2 bg-white text-sm">
            <option value="">All statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
    </form>
    <a href="{{ route('loans.create') }}" class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
        New loan
    </a>
</div>

<div class="mt-6 border border-paper-line bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-paper-line text-left text-ink-soft">
                <th class="px-4 py-3 font-medium">Loan No.</th>
                <th class="px-4 py-3 font-medium">Customer</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium text-right">Principal</th>
                <th class="px-4 py-3 font-medium text-right">Outstanding</th>
                <th class="px-4 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                <tr class="border-b border-paper-line last:border-0 hover:bg-paper/50">
                    <td class="px-4 py-3"><a href="{{ route('loans.show', $loan) }}" class="text-ink font-medium hover:underline tabular">#{{ $loan->loan_no }}</a></td>
                    <td class="px-4 py-3">{{ $loan->customer->name }}</td>
                    <td class="px-4 py-3 text-ink-soft">{{ $loan->loan_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right tabular">₹{{ number_format($loan->principal_paise / 100) }}</td>
                    <td class="px-4 py-3 text-right tabular">₹{{ number_format($loan->outstanding_balance_paise / 100) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-medium px-2 py-1 rounded-sm {{ $loan->status === 'active' ? 'text-success bg-success/10' : 'text-ink-soft bg-paper' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-ink-soft">No loans found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $loans->links() }}</div>

@endsection
