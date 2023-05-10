<?php

namespace App\Services;

use App\Enums\DepositFeeEnum;
use App\Enums\OperationTypeEnum;
use Illuminate\Support\Facades\Http;

class CalculateCommissionFeeService
{
    protected $dateService;
    protected $calculateService;
    protected $withdrawService;
    protected $convertCurrencyService;

    public function __construct(
        DateConverterService $dateConverterService,
        CalculateService $calculateFeeService,
        WithdrawCommissionFeeService $withdrawCommissionFeeService,
        ConvertCurrencyService $convertCurrencyService
    ) {
        $this->dateService = $dateConverterService;
        $this->calculateService = $calculateFeeService;
        $this->withdrawService = $withdrawCommissionFeeService;
        $this->convertCurrencyService = $convertCurrencyService;
    }

    public function commissionFee($rows): array
    {
        $commission_fee_array = [];
        $commission_fee = 0;
        // calculate commission fees for withdraw/deposit
        foreach ($rows as $row) {
            $row[0] = $this->dateService->toDateTime($row[0]);
            $commission_fee = $this->convertCurrencyService->convertToEUR($row[5], $row[4]);
            if ($row[3] === OperationTypeEnum::DEPOSIT->value) {
                $commission_fee = $this->calculateService->calculateCommissionFee($commission_fee, DepositFeeEnum::DEPOSIT_FEE);
            } elseif ($row[3] === OperationTypeEnum::WITHDRAW->value) {
                $commission_fee = $this->withdrawService->withdrawCommissionFee($rows, $row);
            }
            $commission_fee_array[] = $this->calculateService->roundUpCommissionFee(
                $this->convertCurrencyService->convertFromEUR($row[5], $commission_fee)
            );

        }
        return $commission_fee_array;
    }
}
