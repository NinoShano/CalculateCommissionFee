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

    public function withdrawCommissionFee($users_transactions, $transaction)
    {
        $transactions_count = 0;
        $transactions_amount = 0;
        $current_transaction_amount = 0;

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
            $start_date = new Carbon($transaction[0]);
            $start_date = $start_date->startOfWeek();
            // End : Saturday
            $end_date = new Carbon($transaction[0]);
            $end_date = $end_date->endOfWeek();

            // Calculate user transactions history
            foreach ($users_transactions as $key => $user_transaction) {
                $current_transaction_date = new Carbon($user_transaction[0]);

                // Check if transaction date is in current week range
                if ($user_transaction[1] === $transaction[1] &&
                    ($current_transaction_date->gte($start_date) && $current_transaction_date->lte($end_date))) {
                    // increase transaction number
                    ++$transactions_count;
                    $transactions_amount += $user_transaction[4];


                }
//                if (array_intersect($user_transaction->toArray(), $transaction->toArray())){
//                    dump("daemtxva");
//                    break;
//                }
            }
            $historical_transactions_amount = $transactions_amount - $transaction[4];
            if ($transactions_count > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_QUANTITY ||
                $historical_transactions_amount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                return $this->calculateService->calculateCommissionFee(
                    $transaction[4],
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            }
            if ($transactions_amount > WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT) {
                $current_transaction_amount = $transaction[4] - (WithdrawFeeEnum::FREE_OF_CHARGE_OPERATION_AMOUNT - $historical_transactions_amount);
                return $this->calculateService->calculateCommissionFee(
                    $current_transaction_amount,
                    WithdrawFeeEnum::WITHDRAW_FEE_FOR_PRIVATE_CLIENT
                );
            }
        }
    }
}
