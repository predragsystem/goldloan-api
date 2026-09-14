@extends('layouts.app')

@section('title', 'New loan')

@section('content')

<form method="POST" action="{{ route('loans.store') }}" id="loan-form" class="max-w-3xl space-y-8">
    @csrf

    <section>
        <h2 class="font-display text-lg text-ink mb-4">Borrower &amp; terms</h2>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Customer</label>
                <select name="customer_id" required class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
                    <option value="">Select a customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $selectedCustomerId == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} — {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-ink-soft">
                    New customer? <a href="{{ route('customers.create') }}" class="underline">Add them first</a>.
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Loan date</label>
                <input type="date" name="loan_date" value="{{ old('loan_date', now()->toDateString()) }}" required
                       class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Loan amount (₹)</label>
                <input type="number" id="principal_rupees" min="1" step="1" required
                       class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white tabular focus:outline-none focus:ring-2 focus:ring-brass">
                <input type="hidden" name="principal_paise" id="principal_paise">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Interest rate (% / month)</label>
                <input type="number" name="interest_rate_percent" step="0.01" min="0" required
                       class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white tabular focus:outline-none focus:ring-2 focus:ring-brass">
            </div>
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-lg text-ink">Pledged items</h2>
            <button type="button" id="add-item" class="text-sm font-medium text-ink underline">+ Add item</button>
        </div>

        <div id="items-container" class="space-y-4"></div>

        <template id="item-row-template">
            <div class="item-row border border-paper-line bg-white p-4 grid sm:grid-cols-5 gap-3 items-end">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-ink-soft mb-1">Type</label>
                    <select name="items[__i__][jewellery_type_id]" required class="w-full border border-paper-line rounded-sm px-2 py-1.5 bg-white text-sm">
                        @foreach ($jewelleryTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-ink-soft mb-1">Quality</label>
                    <select name="items[__i__][jewellery_quality_id]" required class="w-full border border-paper-line rounded-sm px-2 py-1.5 bg-white text-sm">
                        @foreach ($jewelleryQualities as $quality)
                            <option value="{{ $quality->id }}">{{ $quality->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-soft mb-1">Qty</label>
                    <input type="number" name="items[__i__][quantity]" min="1" value="1" required class="w-full border border-paper-line rounded-sm px-2 py-1.5 bg-white text-sm tabular">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-soft mb-1">Grams</label>
                    <input type="number" name="items[__i__][total_grams]" step="0.001" min="0" required class="w-full border border-paper-line rounded-sm px-2 py-1.5 bg-white text-sm tabular">
                </div>
                <div class="flex gap-2">
                    <input type="text" name="items[__i__][description]" placeholder="Description" class="w-full border border-paper-line rounded-sm px-2 py-1.5 bg-white text-sm">
                    <button type="button" class="remove-item text-alert text-sm">✕</button>
                </div>
            </div>
        </template>
    </section>

    <div class="flex gap-3">
        <button type="submit" class="bg-ink text-paper px-5 py-2.5 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
            Create loan
        </button>
        <a href="{{ route('loans.index') }}" class="px-5 py-2.5 text-sm text-ink-soft hover:text-ink">Cancel</a>
    </div>
</form>

<script>
(function () {
    const container = document.getElementById('items-container');
    const template = document.getElementById('item-row-template');
    let index = 0;

    function addRow() {
        const html = template.innerHTML.replaceAll('__i__', index);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.appendChild(wrapper.firstElementChild);
        index++;
    }

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });

    document.getElementById('add-item').addEventListener('click', addRow);
    addRow(); // start with one row

    document.getElementById('loan-form').addEventListener('submit', function () {
        const rupees = parseFloat(document.getElementById('principal_rupees').value || '0');
        document.getElementById('principal_paise').value = Math.round(rupees * 100);
    });
})();
</script>

@endsection
