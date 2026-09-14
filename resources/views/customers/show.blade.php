@extends('layouts.app')

@section('title', $customer->name)

@section('content')

<div class="grid lg:grid-cols-3 gap-8">
    <div class="border border-paper-line bg-white p-6">
        <h2 class="font-display text-lg text-ink">{{ $customer->name }}</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-ink-soft">Phone</dt><dd class="tabular">{{ $customer->phone }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-soft">Gender</dt><dd>{{ ucfirst($customer->gender ?? '—') }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-soft">Aadhar</dt><dd class="tabular">{{ $customer->aadhar_no ?? '—' }}</dd></div>
            <div><dt class="text-ink-soft">Address</dt><dd class="mt-1">{{ $customer->address ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-lg text-ink">Loan history</h2>
            <a href="{{ route('loans.create', ['customer_id' => $customer->id]) }}"
               class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                New loan for {{ $customer->name }}
            </a>
        </div>

        <div class="border border-paper-line bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-paper-line text-left text-ink-soft">
                        <th class="px-4 py-3 font-medium">Loan No.</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium text-right">Principal</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr class="border-b border-paper-line last:border-0 hover:bg-paper/50">
                            <td class="px-4 py-3"><a href="{{ route('loans.show', $loan) }}" class="text-ink font-medium hover:underline tabular">#{{ $loan->loan_no }}</a></td>
                            <td class="px-4 py-3 text-ink-soft">{{ $loan->loan_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right tabular">₹{{ number_format($loan->principal_paise / 100) }}</td>
                            <td class="px-4 py-3">{{ ucfirst($loan->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-ink-soft">No loans yet for this customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
