<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class CommissionFeeTest extends TestCase
{
    public function test_calculate_commission_fee_correctly()
    {
        // Store some data in cache
//        $expected_data = ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR'];
//        Cache::put('expected_data', $expected_data, 60);
//
//        $value = Cache::get('expected_data');
//        $actual = Cache::get('fees');
//
//        $this->assertEquals($value, $actual);
       // Import the Excel file
        $file = storage_path('app/files/example.xlsx');
        $transactions = Excel::toArray([], $file)[0];
//        $deposit_expected = ['2014-12-31', '4', 'private', 'deposit', '1200.00', 'EUR'];
//        $withdraw_private_expected = ['2014-12-31', '1', 'private', 'withdraw', '1200.00', 'EUR'];
//        $withdraw_business_expected = ['2014-12-31', '3', 'business', 'withdraw', '1200.00', 'EUR'];

//        $response = $this->post('/import_commissions', [
//            'file' => new UploadedFile($file, 'example.xlsx', 'application/vnd.ms-excel', null, true),
//        ]);

        // Define the commission free depend on operation type and user type
        $depositRate = 0.03 / 100;
        $privateWithdrawRate = 0.3 / 100;
        $businessWithdrawRate = 0.5 / 100;
        $weeklyFreeAmount = 1000.00;
        $fee = [];

//        foreach ($transactions as $transition) {
//            if ($transition[3] === 'deposit') {
//                $fee[] = $transition[4] * $depositRate;
//            } else {
//                if ($transition[3] === 'withdraw') {
//                    if ($transition[2] === 'private') {
//                        $fee[] = $transition[4] * $privateWithdrawRate;
//                    } else {
//                        if ($transition[2] === 'business') {
//
//                        }
//                    }
//                }
//            }
//        }

//        $response->assertStatus(302);
//        $response->assertRedirect('/');
//        $response->assertImported('example.xlsx');
    }
}
