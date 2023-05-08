<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CommissionImport implements ToCollection, WithChunkReading
{
    /*
     * Class handle Excel import
     *
     */
    public function collection(Collection $rows)
    {
        //  calculate withdraw/deposit commission fees

        $commission_fee = [];

        // read currencies json file for convert currency into  EUR
        $json = file_get_contents(storage_path('locale/currency-exchange-rates.json'));
        $json = json_decode($json, true);

        // grouped users transactions
        $users_transaction = [];
        foreach ($rows as $key => $row) {
            // created list for first user transaction
            if (isset($users_transactions[$rows[$key][1]])) {
                $users_transactions[$rows[$key][1]][] = $row;
            } else {
                // append transaction into user list
                $users_transactions[$rows[$key][1]] = [$row];
            }
        }
        // calculate commission fees for withdraw/deposit
        foreach ($rows as $row) {
            $row[0] = $this->toDateTime($row[0]);
            $row[4] /= $json['rates'][$row[5]];
            if ($row[3] === 'deposit') {
                $commission_fee[] = $this->CalculateCommissionFee($row[4], 0.03);
            } elseif ($row[3] === 'withdraw') {
                $commission_fee[] = $this->withdrawCommissionFee($users_transactions[$row[1]], $row);
            }
        }
        // save commission fees into cache
        Cache::put('fees', $commission_fee, 15);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /*
     * Change date format using Carbon object
     */
    public function toDateTime($row)
    {
        $operationDate = Date::excelToDateTimeObject($row);
        $operationDate = new Carbon($operationDate);
        $operation_day = Carbon::parse($operationDate)->dayName;
        return Carbon::parse($operationDate)->format('Y-m-d');
    }

    public function withdrawCommissionFee($user_transactions, $transaction)
    {
        $transaction_count = 0;
        $transaction_amount = 0;
        $current_transaction_amount = 0;

        // check user type
        if ($transaction[2] === 'business') {
            // calculate commission fee
            return $this->CalculateCommissionFee($transaction[4], 0.5);
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
            foreach ($user_transactions as $key => $user_transaction) {
                $transac_date = new Carbon($user_transaction[0]);
                // Check if transaction date is in current week range
                if ($transac_date->gte($start_date) && $transac_date->lte($end_date)
                ) {
                    // increase transaction number
                    ++$transaction_count;
                    // If user's history transaction and current transaction matched, save amount and break the loop
                    if ($key === array_search($transaction, $user_transactions, true)) {
                        $current_transaction_amount = $user_transaction[4];
                        break;
                    }
                    // add current transaction amount to user's transaction total amount
                    $transaction_amount += $user_transaction[4];
                }
            }
        }
        // Apply commission rules
        // Commission fee - 0.3% withdrawn amount for 4th and the following transactions
        if (($transaction_amount > 1000 && $transaction_count > 3) || $transaction_amount > 1000) {
            return $this->CalculateCommissionFee($transaction[4], 0.3);
        }
        // Commission fee is calculated only for the exceeded amount
        if ($transaction_amount + $current_transaction_amount > 1000) {
            return $this->CalculateCommissionFee($current_transaction_amount - (1000 - $transaction_amount), 0.3);
        }
    }

    /*
     * calculate and round up commission fees
     */
    public function CalculateCommissionFee($number, $percentage)
    {
        $num = $number * $percentage / 100;
        return number_format(ceil($num * 100) / 100, 2);
    }
}
