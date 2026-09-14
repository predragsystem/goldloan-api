@extends('layouts.app')

@section('title', 'Edit ' . $customer->name)

@section('content')
<div class="max-w-lg">
    @php($existingPhotoUrl = $customer->photo_url)

    <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')
        @include('customers._photo-input')

        <div>
            <label class="block text-sm font-medium text-ink mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Gender</label>
                <select name="gender" class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
                    <option value="">—</option>
                    <option value="male" {{ old('gender', $customer->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $customer->gender) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required
                       class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Address</label>
            <textarea name="address" rows="2"
                      class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">{{ old('address', $customer->address) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1">Aadhar number</label>
            <input type="text" name="aadhar_no" value="{{ old('aadhar_no', $customer->aadhar_no) }}"
                   class="w-full border border-paper-line rounded-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brass">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-ink text-paper px-5 py-2.5 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                Save changes
            </button>
            <a href="{{ route('customers.show', $customer) }}" class="px-5 py-2.5 text-sm text-ink-soft hover:text-ink">Cancel</a>
        </div>
    </form>

    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="mt-4"
          onsubmit="return confirm('Delete {{ $customer->name }}? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm text-alert underline">Delete this customer</button>
    </form>
</div>
@endsection
