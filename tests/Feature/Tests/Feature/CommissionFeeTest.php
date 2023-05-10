<?php

namespace Tests\Feature;

use App\Enums\DepositFeeEnum;
use App\Enums\WithdrawFeeEnum;
use App\Services\CalculateService;
use App\Services\ConvertCurrencyService;
use Tests\TestCase;

class CommissionFeeTest extends TestCase
{
    public function test_currency_exchange(): void
    {
        $amount = 100;
        $fromCurrency = 'USD';
        $exchangeRate = 1.129031;
        $expected_to = $amount / $exchangeRate;
        $expected_from = $amount * $exchangeRate;

        $result_to = app(ConvertCurrencyService::class)->convertToEUR($fromCurrency, $amount);
        $result_from = app(ConvertCurrencyService::class)->convertFromEUR($fromCurrency, $amount);

        $this->assertEquals($result_to, $expected_to);
        $this->assertEquals($result_from, $expected_from);
    }

    public function test_deposit_commission_fee(): void
    {
        $deposit_expected = ['2014-12-31', '4', 'private', 'deposit', 1200.00, 'EUR'];

        $result = app(CalculateService::class)->calculateCommissionFee(
            $deposit_expected[4],
            DepositFeeEnum::DEPOSIT_FEE
        );

        $this->assertEquals(0.36, $result);
    }

    public function test_withdraw_business_client_commission_fee(): void
    {
        $withdraw_expected = ['2016-01-07', '1', 'business', 'withdraw', 1000.00, 'EUR'];

        $result = app(CalculateService::class)->calculateCommissionFee(
            $withdraw_expected[4],
            WithdrawFeeEnum::WITHDRAW_FEE_FOR_BUSINESS_CLIENT
        );

        $this->assertEquals(5, $result);
    }

    public function test_exceeded_withdraw_private_client_commission_fee(): void
    {
        $withdraw_expected = [
            ['2016-01-07', '1', 'private', 'withdraw', 1000.00, 'EUR'],
            ['2016-01-08', '1', 'private', 'withdraw', 1000.00, 'EUR']
        ];

        foreach ($withdraw_expected as $transaction) {
            $result = app(CalculateService::class)->calculateCommissionFee(
                $transaction[4],
                WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
            );
        }

        $this->assertEquals(3, $result);
    }

    public function test_operation_exceeded_withdraw_private_client_commission_fee(): void
    {
        $withdraw_expected = [
            ['2016-01-07', '1', 'private', 'withdraw', 100.00, 'EUR'],
            ['2016-01-08', '1', 'private', 'withdraw', 100.00, 'EUR'],
            ['2016-01-09', '1', 'private', 'withdraw', 100.00, 'EUR'],
            ['2016-01-10', '1', 'private', 'withdraw', 100.00, 'EUR'],
            ['2016-01-11', '1', 'private', 'withdraw', 100.00, 'EUR']
        ];

        foreach ($withdraw_expected as $transaction) {
            $result = app(CalculateService::class)->calculateCommissionFee(
                $transaction[4],
                WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
            );
        }

        $this->assertEquals(0.3, $result);
    }

}
