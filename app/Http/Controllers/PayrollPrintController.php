<?php

namespace App\Http\Controllers;

use App\Models\PayrollParent;
use Illuminate\Http\Request;

class PayrollPrintController extends Controller
{
    public function print($id)
    {
        $payrollParent = PayrollParent::with(['payrolls.teacher'])->findOrFail($id);

        $settings = appReportSettings();

        return view('payrolls.print', compact('payrollParent', 'settings'));
    }
}
