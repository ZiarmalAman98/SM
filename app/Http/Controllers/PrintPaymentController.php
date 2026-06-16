<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PrintPaymentController extends Controller
{
    public function __invoke(Payment $payment)
    {
        return view('print.payment', compact('payment'));
    }
}
