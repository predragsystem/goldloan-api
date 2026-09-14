<?php

namespace App\Services;

use App\Models\Loan;
use App\Services\Interest\DailyProportionalInterestStrategy;
use App\Services\Interest\FlatMonthlyInterestStrategy;
use App\Services\Interest\InterestStrategy;
use Carbon\Carbon;

/**
 * Single authoritative place that computes accrued interest on a loan —
 * replaces the four near-duplicate inline calculations found across the
 * old app (JewelleryLoanPayment / JewelleryLoanClose / NewExtraLoan / ...).
 * The strategy used is chosen per-tenant (Tenant::interest_calculation_mode).
 */
class InterestCalculator
{
    /**
     * Accrued interest as of a given date, counted from the later of the
     * loan's start date or its most recent transaction (a payment resets
     * the accrual clock — you never charge interest twice on the same days).
     */
    public function accruedInterest(Loan $loan, ?Carbon $asOf = null): int
    {
        $asOf ??= now();

        $periodStart = $loan->transactions()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->value('transaction_date') ?? $loan->loan_date;

        $days = max(0, Carbon::parse($periodStart)->diffInDays($asOf));

        return $this->strategyFor($loan)->calculate(
            $loan->outstanding_balance_paise,
            $loan->interest_rate_percent,
            $days
        );
    }

    private function strategyFor(Loan $loan): InterestStrategy
    {
        return match ($loan->tenant->interest_calculation_mode) {
            'daily_proportional' => new DailyProportionalInterestStrategy,
            default => new FlatMonthlyInterestStrategy,
        };
    }
}
