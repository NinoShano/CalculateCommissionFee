<?php

namespace App\Http\Controllers;

use App\Imports\CommissionImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\CalculateCommissionFeeService;


class CommissionController extends Controller
{
    protected $service;

    public function __construct(CalculateCommissionFeeService $calculateCommissionFeeService)
    {
        $this->service = $calculateCommissionFeeService;
    }

    public function importCommission(Request $request)
    {
        $import = new CommissionImport($this->service);
        Excel::import($import, $request->file('importFile'));
        $data = $import->getData();

        redirect()->back();

        return view('welcome', [
            'commissionFees' => $data,
        ]);
    }
}
