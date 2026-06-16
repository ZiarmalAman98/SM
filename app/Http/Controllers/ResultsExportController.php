<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ExportResultsJob;

class ResultsExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'class_id'  => 'required|integer|exists:school_classes,id',
        ]);

        $job = new ExportResultsJob($request->branch_id, $request->class_id);
        dispatch($job);

        return response()->json([
            'message' => 'Export started. File will be available shortly.',
            'file_name' => $job->getFileName()
        ]);
    }
}
