<?php

namespace App\Services;

use App\Enums\OperationTypeEnum;
use App\Enums\WithdrawFeeEnum;
use Carbon\Carbon;

class WithdrawCommissionFeeService
{
    protected $calculateService;
    protected $convertCurrencyService;

    public function __construct(CalculateService $roundedUpFeeService, ConvertCurrencyService $convertCurrencyService)
    {
        $this->calculateService = $roundedUpFeeService;
        $this->convertCurrencyService = $convertCurrencyService;
    }

    public function withdrawCommissionFee($usersTransactions, $transaction)
    {
        $transactionsCount = 0;
        $transactionsAmount = 0;
        $currentTransactionAmount = 0;

        // check user type
        if ($transaction[2] === 'business') {
            // calculate commission fee
            return $this->calculateService->calculateCommissionFee(
                $transaction[4],
                WithdrawFeeEnum::WITHDRAW_FEE_FOR_BUSINESS_CLIENT
            );
        }

        if ($transaction[2] === 'private') {
            $currentTransactionAmount = $this->convertCurrencyService->convertToEUR($transaction[5], $transaction[4]);
            // Defined range of current week for commission fee rule
            // Start : Monday
            $startDate = new Carbon($transaction[0]);
            $startDate = $startDate->startOfWeek();
            // End : Saturday
            $endDate = new Carbon($transaction[0]);
            $endDate = $endDate->endOfWeek();

            // Calculate user transactions history
            foreach ($usersTransactions as $userTransaction) {
                $currentTransactionDate = new Carbon($userTransaction[0]);

                // Check if transaction date is in current week range
                if ($userTransaction[1] === $transaction[1] &&
                    $userTransaction[3] === OperationTypeEnum::WITHDRAW->value &&
                    ($currentTransactionDate->gte($startDate) && $currentTransactionDate->lte($endDate))) {
                    $exchanged_currencies = 0;

                    if ($userTransaction[5] !== 'EUR') {
                        $exchanged_currencies = $this->convertCurrencyService->convertToEUR(
                            $userTransaction[5],
                            $userTransaction[4]
                        );
                    } else {
                        $exchanged_currencies = $userTransaction[4];
                    }
                    // increase transaction number
                    ++$transactionsCount;

                    $transactionsAmount += $exchanged_currencies;
                }
            }

            $historicalTransactionsAmount = $transactionsAmount - $currentTransactionAmount;

            $exchangedCommissionFee = 0;

            if ($transactionsCount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_QUANTITY ||
                $historicalTransactionsAmount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                $exchangedCommissionFee = $this->calculateService->calculateCommissionFee(
                    $currentTransactionAmount,
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            } elseif ($transactionsAmount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                $currentTransactionAmount -= abs(
                    $historicalTransactionsAmount - WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT
                );
                $exchangedCommissionFee = $this->calculateService->calculateCommissionFee(
                    $currentTransactionAmount,
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            }

            return $this->convertCurrencyService->convertFromEUR(
                $transaction[5],
                $exchangedCommissionFee
            );
        }
    }
}
