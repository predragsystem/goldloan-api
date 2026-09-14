<?php

namespace App\Services\Interest;

/**
 * Charges a full month's interest for any part of a month elapsed, with a
 * minimum of one month — matches the old app's / typical pawnbroker
 * convention (e.g. day 31 of month 2 is billed as 2 full months, not 1.03).
 */
class FlatMonthlyInterestStrategy implements InterestStrategy
{
    public function calculate(int $principalPaise, float $ratePercent, int $days): int
    {
        $months = max(1, (int) ceil($days / 30));
        $monthlyInterest = $principalPaise * $ratePercent / 100;

        return (int) round($monthlyInterest * $months);
    }
}
