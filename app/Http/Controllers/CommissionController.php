<?php

namespace App\Http\Controllers;

use App\Imports\CommissionImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CommissionController extends Controller
{
    public function importCommission(Request $request)
    {
        Excel::import(new CommissionImport(), $request->file('importFile'));

        return redirect('/')->with('success', 'All good!');
    }
}
