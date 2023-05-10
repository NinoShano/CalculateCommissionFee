<?php

namespace App\Enums;

class WithdrawFeeEnum
{
    public const WITHDRAW_FEE_FOR_BUSINESS_CLIENT = 0.5 / 100;
    public const WITHDRAW_FEE_FOR_PRIVATE_CLIENT = 0.3 / 100;
    public const FREE_OF_CHARGE_OPERATION_AMOUNT = 1000;
    public const FREE_OF_CHARGE_OPERATION_QUANTITY = 3;
}
