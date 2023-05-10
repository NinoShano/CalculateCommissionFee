<?php

namespace App\Services;

use App\Enums\WithdrawFeeEnum;
use Carbon\Carbon;

class WithdrawCommissionFeeService
{
    protected $calculateService;

    public function __construct(CalculateService $roundedUpFeeService)
    {
        $this->calculateService = $roundedUpFeeService;
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
            // Defined range of current week for commission fee rule
            // Start : Monday
            $startDate = new Carbon($transaction[0]);
            $startDate = $startDate->startOfWeek();
            // End : Saturday
            $endDate = new Carbon($transaction[0]);
            $endDate = $endDate->endOfWeek();

            // Calculate user transactions history
            foreach ($usersTransactions as $key => $userTransaction) {
                $currentTransactionDate = new Carbon($userTransaction[0]);

                // Check if transaction date is in current week range
                if ($userTransaction[1] === $transaction[1] &&
                    ($currentTransactionDate->gte($startDate) && $currentTransactionDate->lte($endDate))) {
                    // increase transaction number
                    ++$transactionsCount;
                    $transactionsAmount += $userTransaction[4];
                }
//                if (array_intersect($userTransaction->toArray(), $transaction->toArray())){
//                    break;
//                }
            }
            $historicalTransactionsAmount = $transactionsAmount - $transaction[4];

            if ($transactionsCount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_QUANTITY ||
                $historicalTransactionsAmount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                return $this->calculateService->calculateCommissionFee(
                    $transaction[4],
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            }

            if ($transactionsAmount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                $currentTransactionAmount = $transaction[4] - (WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT - $historicalTransactionsAmount);
                return $this->calculateService->calculateCommissionFee(
                    $currentTransactionAmount,
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            }
        }
    }
}
