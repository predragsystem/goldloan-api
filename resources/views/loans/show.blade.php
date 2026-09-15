@extends('layouts.app')

@section('title', 'Loan #' . $loan->loan_no)

@section('content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h2 class="font-display text-2xl text-ink">Loan #{{ $loan->loan_no }}</h2>
        <p class="text-ink-soft">
            <a href="{{ route('customers.show', $loan->customer) }}" class="hover:underline">{{ $loan->customer->name }}</a>
            · {{ $loan->loan_date->format('d M Y') }}
        </p>
    </div>
    <span class="text-xs font-medium px-3 py-1.5 rounded-sm {{ $loan->status === 'active' ? 'text-success bg-success/10' : 'text-ink-soft bg-paper' }}">
        {{ ucfirst($loan->status) }}
    </span>
</div>

<div class="grid lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 space-y-8">

        <div class="border border-paper-line bg-white">
            <div class="px-4 py-3 border-b border-paper-line font-medium text-ink">Pledged items</div>
            <div class="overflow-x-auto">
<table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-paper-line text-left text-ink-soft">
                        <th class="px-4 py-2 font-medium">Type</th>
                        <th class="px-4 py-2 font-medium">Quality</th>
                        <th class="px-4 py-2 font-medium text-right">Qty</th>
                        <th class="px-4 py-2 font-medium text-right">Grams</th>
                        <th class="px-4 py-2 font-medium">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loan->items as $item)
                        <tr class="border-b border-paper-line last:border-0">
                            <td class="px-4 py-2">{{ $item->jewelleryType->name }}</td>
                            <td class="px-4 py-2">{{ $item->jewelleryQuality->name }}</td>
                            <td class="px-4 py-2 text-right tabular">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right tabular">{{ $item->total_grams }}</td>
                            <td class="px-4 py-2 text-ink-soft">{{ $item->description ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</div>
        </div>

        <div class="border border-paper-line bg-white">
            <div class="px-4 py-3 border-b border-paper-line font-medium text-ink">Transaction history</div>
            <div class="overflow-x-auto">
<table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-paper-line text-left text-ink-soft">
                        <th class="px-4 py-2 font-medium">Date</th>
                        <th class="px-4 py-2 font-medium">Type</th>
                        <th class="px-4 py-2 font-medium text-right">Amount</th>
                        <th class="px-4 py-2 font-medium text-right">Interest</th>
                        <th class="px-4 py-2 font-medium text-right">Principal</th>
                        <th class="px-4 py-2 font-medium text-right">Balance after</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $tx)
                        <tr class="border-b border-paper-line last:border-0">
                            <td class="px-4 py-2 text-ink-soft">{{ $tx->transaction_date->format('d M Y') }}</td>
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</td>
                            <td class="px-4 py-2 text-right tabular">₹{{ number_format($tx->amount_paise / 100) }}</td>
                            <td class="px-4 py-2 text-right tabular text-ink-soft">₹{{ number_format($tx->interest_component_paise / 100) }}</td>
                            <td class="px-4 py-2 text-right tabular text-ink-soft">₹{{ number_format($tx->principal_component_paise / 100) }}</td>
                            <td class="px-4 py-2 text-right tabular font-medium">₹{{ number_format($tx->balance_after_paise / 100) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="border border-paper-line bg-white p-5">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-ink-soft">Principal</dt><dd class="tabular">₹{{ number_format($loan->principal_paise / 100) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-soft">Outstanding</dt><dd class="tabular font-medium">₹{{ number_format($loan->outstanding_balance_paise / 100) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-soft">Rate</dt><dd class="tabular">{{ $loan->interest_rate_percent }}% / month</dd></div>
                @if ($loan->status === 'active')
                    <div class="flex justify-between pt-2 border-t border-paper-line"><dt class="text-ink-soft">Accrued interest (today)</dt><dd class="tabular font-medium text-brass-dark">₹{{ number_format($accruedInterest / 100) }}</dd></div>
                @endif
            </dl>
        </div>

        @if ($loan->status === 'active')
            <div class="border border-paper-line bg-white p-5">
                <h3 class="font-medium text-ink mb-3">Take a payment</h3>
                <form method="POST" action="{{ route('loans.pay', $loan) }}" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount_rupees" min="1" required placeholder="₹ amount"
                           class="flex-1 border border-paper-line rounded-sm px-3 py-2 bg-white text-sm tabular"
                           oninput="this.form.amount_paise.value = Math.round((parseFloat(this.value)||0)*100)">
                    <input type="hidden" name="amount_paise">
                    <button type="submit" class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">Record</button>
                </form>
                <p class="mt-2 text-xs text-ink-soft">Applied to accrued interest first; extra reduces principal.</p>
            </div>

            <div class="border border-paper-line bg-white p-5">
                <h3 class="font-medium text-ink mb-3">Top-up loan</h3>
                <form method="POST" action="{{ route('loans.topUp', $loan) }}" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount_rupees" min="1" required placeholder="₹ amount"
                           class="flex-1 border border-paper-line rounded-sm px-3 py-2 bg-white text-sm tabular"
                           oninput="this.form.amount_paise.value = Math.round((parseFloat(this.value)||0)*100)">
                    <input type="hidden" name="amount_paise">
                    <button type="submit" class="border border-ink text-ink px-4 py-2 rounded-sm text-sm font-medium hover:bg-ink hover:text-paper transition-colors">Add</button>
                </form>
            </div>

            <div class="border border-alert/30 bg-alert/5 p-5">
                <h3 class="font-medium text-ink mb-1">Close loan</h3>
                <p class="text-xs text-ink-soft mb-3">
                    Settles outstanding principal (₹{{ number_format($loan->outstanding_balance_paise / 100) }})
                    + accrued interest (₹{{ number_format($accruedInterest / 100) }}) =
                    <span class="font-medium text-ink">₹{{ number_format(($loan->outstanding_balance_paise + $accruedInterest) / 100) }}</span>.
                </p>
                <form method="POST" action="{{ route('loans.close', $loan) }}" onsubmit="return confirm('Close this loan? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="w-full bg-alert text-paper py-2 rounded-sm text-sm font-medium">
                        Close &amp; settle loan
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection
