<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ConvertCurrencyService
{
    public function convertToEUR($currency, $amount): float
    {
        $currencyRate = $this->getCurrencyRate();
        if (!$currencyRate['base'] === $currency) {
            return $amount;
        }

        return $amount / $currencyRate["rates"][$currency];
    }

    public function convertFromEUR($currency, $feeAmount)
    {
        $currencyRate = $this->getCurrencyRate();
        if (!$currencyRate['base'] === $currency) {
            return $feeAmount;
        }

        return $feeAmount * $currencyRate["rates"][$currency];
    }

    public function getCurrencyRate()
    {
        return Http::get(env('API_URL'))->json();
    }
}
