<?php

namespace App\Services\Interest;

interface InterestStrategy
{
    /**
     * @param  int  $principalPaise  outstanding principal the interest is charged on
     * @param  float  $ratePercent  the loan's monthly interest rate, e.g. 2.5 for 2.5%/month
     * @param  int  $days  calendar days elapsed since the last accrual point
     * @return int accrued interest, in paise
     */
    public function calculate(int $principalPaise, float $ratePercent, int $days): int;
}
