<?php

namespace App\Http\Controllers\Web;

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
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(CustomerStoreRequest $request)
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $this->photos->store($request->file('photo'), self::PHOTO_FOLDER);
        }

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)->with('status', 'Customer added.');
    }

    public function show(Customer $customer)
    {
        $loans = $customer->loans()->latest('loan_date')->get();

        return view('customers.show', compact('customer', 'loans'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
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

        return redirect()->route('customers.show', $customer)->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['customer' => 'This customer has existing loans and cannot be deleted.']);
        }

        return redirect()->route('customers.index')->with('status', 'Customer deleted.');
    }
}
