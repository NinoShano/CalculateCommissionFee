<?php

namespace App\Services;

use App\Enums\DepositFeeEnum;
use App\Enums\OperationTypeEnum;

class CalculateCommissionFeeService
{
    protected $dateService;
    protected $calculateService;
    protected $withdrawService;

    public function __construct(
        DateConverterService $dateConverterService,
        CalculateService $calculateFeeService,
        WithdrawCommissionFeeService $withdrawCommissionFeeService
    ) {
        $this->dateService = $dateConverterService;
        $this->calculateService = $calculateFeeService;
        $this->withdrawService = $withdrawCommissionFeeService;
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

            if ($row[3] === OperationTypeEnum::DEPOSIT->value) {
                $commissionFee = $this->calculateService->calculateCommissionFee(
                    $commissionFee,
                    DepositFeeEnum::DEPOSIT_FEE
                );
            } elseif ($row[3] === OperationTypeEnum::WITHDRAW->value) {
                $commissionFee = $this->withdrawService->withdrawCommissionFee($rows, $row);
            }
            $commissionFeeArray[] = $this->calculateService->roundUpCommissionFee($commissionFee);
        }

        return $commissionFeeArray;
    }
}
