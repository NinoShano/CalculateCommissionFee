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

    /*
     * Calculate commission fees for withdraw/deposit
     */
    public function commissionFee($rows): array
    {
        $commissionFeeArray = [];
        $commissionFee = 0;

        foreach ($rows as $row) {
            $row[0] = $this->dateService->toDateTime($row[0]);
            $commissionFee = $this->convertCurrencyService->convertToEUR($row[5], $row[4]);

            if ($row[3] === OperationTypeEnum::DEPOSIT->value) {
                $commissionFee = $this->calculateService->calculateCommissionFee(
                    $commissionFee,
                    DepositFeeEnum::DEPOSIT_FEE
                );
            } elseif ($row[3] === OperationTypeEnum::WITHDRAW->value) {
                $commissionFee = $this->withdrawService->withdrawCommissionFee($rows, $row);
            }
            $commissionFeeArray[] = $this->calculateService->roundUpCommissionFee(
                $this->convertCurrencyService->convertFromEUR($row[5], $commissionFee)
            );
        }

        return $commissionFeeArray;
    }
}
