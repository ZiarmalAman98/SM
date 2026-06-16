<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\FeePayment;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    public function printReceipt(Request $request)
    {
        // Get the selected fee payment IDs from the query parameters
        $feePaymentIds = $request->input('fee_payment_id', []); // Default to empty array if no fee_payment_id is passed

        // Ensure there are fee payment IDs
        if (empty($feePaymentIds)) {
            return redirect()->route('fee_payments.index')->with('error', 'No fee payments selected.');
        }

        // Retrieve the fee payments for the selected fee_payment_ids
        $feePayments = FeePayment::whereIn('id', $feePaymentIds)->get();

        // Pass the fee payments data to the view for printing
        return view('fee_payments.print_receipt', compact('feePayments'));
    }

    public function printFeePayment(Request $request)
    {
        $payments = FeePayment::with(['student', 'class', 'feeType'])
            ->when($request->filled('user_id'), fn($q) => $q->where('student_id', $request->user_id))
            ->when(
                $request->filled('fee_type'),
                fn($q) =>
                $q->whereHas('feeType', fn($sub) => $sub->where('name', $request->fee_type))
            )
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('payment_date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('payment_date', '<=', $request->to_date))
            ->get();

        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('fee_payments.fee_payment', compact('payments', "settings"));
    }
}
