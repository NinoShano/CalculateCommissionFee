<?php

namespace App\Enums;

enum OperationTypeEnum: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
