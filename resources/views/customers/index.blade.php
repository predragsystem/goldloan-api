@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="flex items-center justify-between gap-4">
    <form method="GET" class="flex-1 max-w-sm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone"
               class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-brass">
    </form>
    <a href="{{ route('customers.create') }}" class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
        New customer
    </a>
</div>

<div class="mt-6 border border-paper-line bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-paper-line text-left text-ink-soft">
                <th class="px-4 py-3 font-medium">Name</th>
                <th class="px-4 py-3 font-medium">Phone</th>
                <th class="px-4 py-3 font-medium">Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr class="border-b border-paper-line last:border-0 hover:bg-paper/50">
                    <td class="px-4 py-3">
                        <a href="{{ route('customers.show', $customer) }}" class="text-ink font-medium hover:underline">{{ $customer->name }}</a>
                    </td>
                    <td class="px-4 py-3 tabular">{{ $customer->phone }}</td>
                    <td class="px-4 py-3 text-ink-soft">{{ $customer->address ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-10 text-center text-ink-soft">
                        No customers yet. <a href="{{ route('customers.create') }}" class="text-ink underline">Add the first one</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $customers->links() }}</div>

@endsection
