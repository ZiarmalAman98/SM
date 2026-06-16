<?php

namespace App\Jobs;

use App\Exports\CustomResultsTemplateExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $branchId;
    protected int $classId;
    protected string $fileName;

    public function __construct(int $branchId, int $classId)
    {
        $this->branchId = $branchId;
        $this->classId = $classId;
        $this->fileName = 'results-' . $branchId . '-' . $classId . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
    }

    public function handle(): void
    {
        Excel::store(
            new CustomResultsTemplateExport($this->branchId, $this->classId),
            'exports/' . $this->fileName,
            'public'
        );
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}