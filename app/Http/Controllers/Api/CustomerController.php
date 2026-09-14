<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return $customers;
    }

    public function store(CustomerStoreRequest $request)
    {
        // tenant_id is stamped automatically by the BelongsToTenant trait.
        $customer = Customer::create($request->validated());

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return $customer;
    }

    public function update(CustomerStoreRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return $customer;
    }

    public function loans(Customer $customer)
    {
        return $customer->loans()->with('items')->latest()->get();
    }
}
