<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Services\PhotoUploader;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private const PHOTO_FOLDER = 'customer-photos';

    public function __construct(private PhotoUploader $photos) {}

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
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $this->photos->store($request->file('photo'), self::PHOTO_FOLDER);
        }

        // tenant_id is stamped automatically by the BelongsToTenant trait.
        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return $customer;
    }

    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $data = $request->safe()->except('photo');

        $data['photo_url'] = $this->photos->replace(
            $customer->photo_url,
            $request->file('photo'),
            self::PHOTO_FOLDER
        );

        $customer->update($data);

        return $customer;
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'This customer has existing loans and cannot be deleted.',
            ], 409);
        }

        return response()->json(status: 204);
    }

    public function loans(Customer $customer)
    {
        return $customer->loans()->with('items')->latest()->get();
    }
}
