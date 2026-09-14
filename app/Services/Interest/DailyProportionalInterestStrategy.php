<?php

namespace App\Services\Interest;

/**
 * Charges interest day-by-day: the monthly rate is divided by 30 to get a
 * daily rate, then multiplied by exact calendar days elapsed. Fairer to the
 * customer than flat-monthly, but yields lower interest income for the
 * lender on short overruns into a new month.
 */
class DailyProportionalInterestStrategy implements InterestStrategy
{
    public function calculate(int $principalPaise, float $ratePercent, int $days): int
    {
        $dailyInterest = ($principalPaise * $ratePercent / 100) / 30;

        return (int) round($dailyInterest * $days);
    }
}
