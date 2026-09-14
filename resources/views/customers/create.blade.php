@extends('layouts.app')

@section('title', 'New customer')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('customers.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Gender</label>
                <select name="gender" class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
                    <option value="">—</option>
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                       class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Address</label>
            <textarea name="address" rows="2"
                      class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">{{ old('address') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Aadhar number</label>
            <input type="text" name="aadhar_no" value="{{ old('aadhar_no') }}"
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-ink text-paper px-5 py-2.5 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                Save customer
            </button>
            <a href="{{ route('customers.index') }}" class="px-5 py-2.5 text-sm text-ink-soft hover:text-ink">Cancel</a>
        </div>
    </form>
</div>
@endsection
