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
        return number_format(ceil($number * 100) / 100, 2, ".", "");
    }


}
