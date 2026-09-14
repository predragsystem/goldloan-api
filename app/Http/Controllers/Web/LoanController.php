<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanCloseRequest;
use App\Http\Requests\LoanPaymentRequest;
use App\Http\Requests\LoanStoreRequest;
use App\Http\Requests\LoanTopUpRequest;
use App\Models\Customer;
use App\Models\JewelleryQuality;
use App\Models\JewelleryType;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Services\InterestCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct(private InterestCalculator $interest) {}

    public function index(Request $request)
    {
        $loans = Loan::with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('loan_date')
            ->paginate(15)
            ->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $jewelleryTypes = JewelleryType::orderBy('name')->get();
        $jewelleryQualities = JewelleryQuality::orderBy('name')->get();
        $selectedCustomerId = $request->integer('customer_id') ?: null;

        return view('loans.create', compact('customers', 'jewelleryTypes', 'jewelleryQualities', 'selectedCustomerId'));
    }

    public function store(LoanStoreRequest $request)
    {
        $tenantId = $request->user()->tenant_id;

        $loan = DB::transaction(function () use ($request, $tenantId) {
            $nextLoanNo = (int) Loan::where('tenant_id', $tenantId)->lockForUpdate()->max('loan_no') + 1;

            $loan = Loan::create([
                'loan_no' => $nextLoanNo,
                'customer_id' => $request->customer_id,
                'loan_date' => $request->loan_date,
                'principal_paise' => $request->principal_paise,
                'interest_rate_percent' => $request->interest_rate_percent,
                'status' => 'active',
                'outstanding_balance_paise' => $request->principal_paise,
                'created_by' => $request->user()->id,
            ]);

            foreach ($request->items as $item) {
                LoanItem::create([...$item, 'loan_id' => $loan->id]);
            }

            LoanTransaction::create([
                'loan_id' => $loan->id,
                'type' => 'disbursement',
                'amount_paise' => $request->principal_paise,
                'principal_component_paise' => $request->principal_paise,
                'interest_component_paise' => 0,
                'balance_after_paise' => $request->principal_paise,
                'transaction_date' => $request->loan_date,
                'created_by' => $request->user()->id,
            ]);

            return $loan;
        });

        return redirect()->route('loans.show', $loan)->with('status', "Loan #{$loan->loan_no} created.");
    }

    public function show(Loan $loan)
    {
        $loan->load('items.jewelleryType', 'items.jewelleryQuality', 'customer');
        $transactions = $loan->transactions()->orderBy('transaction_date')->orderBy('id')->get();
        $accruedInterest = $loan->status === 'active' ? $this->interest->accruedInterest($loan) : 0;

        return view('loans.show', compact('loan', 'transactions', 'accruedInterest'));
    }

    public function pay(LoanPaymentRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();
        $accrued = $this->interest->accruedInterest($loan, \Carbon\Carbon::parse($date));

        $interestComponent = min($request->amount_paise, $accrued);
        $principalComponent = $request->amount_paise - $interestComponent;

        $loan->outstanding_balance_paise = max(0, $loan->outstanding_balance_paise - $principalComponent);
        $loan->save();

        LoanTransaction::create([
            'loan_id' => $loan->id,
            'type' => 'interest_payment',
            'amount_paise' => $request->amount_paise,
            'principal_component_paise' => $principalComponent,
            'interest_component_paise' => $interestComponent,
            'balance_after_paise' => $loan->outstanding_balance_paise,
            'transaction_date' => $date,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('loans.show', $loan)->with('status', 'Payment recorded.');
    }

    public function topUp(LoanTopUpRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();

        $loan->principal_paise += $request->amount_paise;
        $loan->outstanding_balance_paise += $request->amount_paise;
        $loan->save();

        LoanTransaction::create([
            'loan_id' => $loan->id,
            'type' => 'top_up',
            'amount_paise' => $request->amount_paise,
            'principal_component_paise' => $request->amount_paise,
            'interest_component_paise' => 0,
            'balance_after_paise' => $loan->outstanding_balance_paise,
            'transaction_date' => $date,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('loans.show', $loan)->with('status', 'Top-up recorded.');
    }

    public function close(LoanCloseRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();
        $accrued = $this->interest->accruedInterest($loan, \Carbon\Carbon::parse($date));
        $payoff = $loan->outstanding_balance_paise + $accrued;

        DB::transaction(function () use ($loan, $accrued, $payoff, $date, $request) {
            LoanTransaction::create([
                'loan_id' => $loan->id,
                'type' => 'closure',
                'amount_paise' => $payoff,
                'principal_component_paise' => $loan->outstanding_balance_paise,
                'interest_component_paise' => $accrued,
                'balance_after_paise' => 0,
                'transaction_date' => $date,
                'created_by' => $request->user()->id,
            ]);

            $loan->update(['status' => 'closed', 'outstanding_balance_paise' => 0]);
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Loan closed and settled.');
    }
}
