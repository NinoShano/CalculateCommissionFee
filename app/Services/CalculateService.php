<?php

namespace App\Services;

class CalculateService
{
    /*
     * Calculate commission fees
     */
    public function calculateCommissionFee($number, $percentage): float
    {
        return $number * $percentage;
    }

    /*
     * Round up commission fees
     */
    public function roundUpCommissionFee($number): float
    {
        return number_format($number, 2, ".", "");
    }


}
