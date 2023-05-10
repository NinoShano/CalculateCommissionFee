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
        Excel::import(new CommissionImport($this->service), $request->file('importFile'));

        return redirect('/')->with('success', 'All good!');
    }
}
