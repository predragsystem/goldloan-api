<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanCloseRequest;
use App\Http\Requests\LoanPaymentRequest;
use App\Http\Requests\LoanStoreRequest;
use App\Http\Requests\LoanTopUpRequest;
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
        return Loan::query()
            ->with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->from, fn ($q) => $q->whereDate('loan_date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('loan_date', '<=', $request->to))
            ->latest('loan_date')
            ->paginate(20);
    }

    /**
     * Creates the loan header, its pledged items, and the initial
     * disbursement transaction in one atomic call — mirrors what the old
     * app's single "New Loan" screen did across three separate inserts.
     */
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

        return response()->json($loan->load('items', 'customer'), 201);
    }

    public function show(Loan $loan)
    {
        return $loan->load('items', 'customer', 'documents');
    }

    public function transactions(Loan $loan)
    {
        return $loan->transactions()->orderBy('transaction_date')->orderBy('id')->get();
    }

    public function interestPreview(Loan $loan)
    {
        return response()->json([
            'as_of' => now()->toDateString(),
            'outstanding_principal_paise' => $loan->outstanding_balance_paise,
            'accrued_interest_paise' => $this->interest->accruedInterest($loan),
        ]);
    }

    /**
     * Interest / partial payment. The payment is applied to accrued
     * interest first; anything paid beyond that reduces principal.
     */
    public function pay(LoanPaymentRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();
        $accrued = $this->interest->accruedInterest($loan, \Carbon\Carbon::parse($date));

        $interestComponent = min($request->amount_paise, $accrued);
        $principalComponent = $request->amount_paise - $interestComponent;

        $loan->outstanding_balance_paise = max(0, $loan->outstanding_balance_paise - $principalComponent);
        $loan->save();

        $transaction = LoanTransaction::create([
            'loan_id' => $loan->id,
            'type' => 'interest_payment',
            'amount_paise' => $request->amount_paise,
            'principal_component_paise' => $principalComponent,
            'interest_component_paise' => $interestComponent,
            'balance_after_paise' => $loan->outstanding_balance_paise,
            'transaction_date' => $date,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($transaction, 201);
    }

    /**
     * Additional disbursement against an existing pledge — increases both
     * principal and outstanding balance.
     */
    public function topUp(LoanTopUpRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();

        $loan->principal_paise += $request->amount_paise;
        $loan->outstanding_balance_paise += $request->amount_paise;
        $loan->save();

        $transaction = LoanTransaction::create([
            'loan_id' => $loan->id,
            'type' => 'top_up',
            'amount_paise' => $request->amount_paise,
            'principal_component_paise' => $request->amount_paise,
            'interest_component_paise' => 0,
            'balance_after_paise' => $loan->outstanding_balance_paise,
            'transaction_date' => $date,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($transaction, 201);
    }

    /**
     * Final settlement: pays off accrued interest + remaining principal in
     * one shot and closes the loan.
     */
    public function close(LoanCloseRequest $request, Loan $loan)
    {
        $date = $request->transaction_date ?? now()->toDateString();
        $accrued = $this->interest->accruedInterest($loan, \Carbon\Carbon::parse($date));
        $payoff = $loan->outstanding_balance_paise + $accrued;

        $transaction = DB::transaction(function () use ($loan, $accrued, $payoff, $date, $request) {
            $transaction = LoanTransaction::create([
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

            return $transaction;
        });

        return response()->json($transaction, 201);
    }
}
