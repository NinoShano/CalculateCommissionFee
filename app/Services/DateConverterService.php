<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DateConverterService
{
    /*
     * Format date using Carbon object
     */
    public function toDateTime($row): string
    {
        $operationDate = Date::excelToDateTimeObject((int)$row);
        $operationDate = new Carbon($operationDate);

        return Carbon::parse($operationDate)->format('Y-m-d');
    }
}
