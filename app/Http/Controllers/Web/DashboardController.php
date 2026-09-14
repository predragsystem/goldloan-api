<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $activeLoans = Loan::where('status', 'active');

        $summary = [
            'active_loan_count' => (clone $activeLoans)->count(),
            'outstanding_paise' => (clone $activeLoans)->sum('outstanding_balance_paise'),
            'customer_count' => Customer::count(),
            'collected_this_month_paise' => LoanTransaction::whereIn('type', ['interest_payment', 'closure'])
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->whereHas('loan') // stays tenant-scoped via Loan's global scope
                ->sum('amount_paise'),
        ];

        $recentLoans = Loan::with('customer')->latest('loan_date')->limit(8)->get();

        return view('dashboard', compact('summary', 'recentLoans'));
    }
}
