<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Branch;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class FormattedResultsController extends Controller
{
    public function download(Request $request)
    {
        // ✅ Validate inputs
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'class_id'  => 'required|integer|exists:school_classes,id',
        ]);

        $branch = Branch::findOrFail($request->branch_id);
        $class  = SchoolClass::findOrFail($request->class_id);

        // ✅ Get all active students in that class
        $students = User::whereHas('studentClasses', function ($q) use ($class) {
            $q->where('class_id', $class->id)->where('status', 'active');
        })->get();

        // ✅ Load the Excel template
        $templatePath = public_path('documents/result.xlsx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template not found at public/documents/result.xlsx');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        /**
         * 🔹 Adjust row/column positions based on your template.
         * Example:
         *  - A2 = Serial
         *  - B2 = Student Name
         *  - C2 = Father Name
         */
        $startRow = 2;
        $row = $startRow;

        foreach ($students as $index => $student) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $student->name);
            $sheet->setCellValue("C{$row}", $student->father_name ?? '');

            $this->applyFormatting($sheet, $row);
            $row++;
        }

        // ✅ Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Generate filename
        $filename = 'Student_List_' . $branch->branch_name . '_' . $class->class_name . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        // ✅ Download the file
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    /**
     * 🔹 Optional formatting (border + center align)
     */
    private function applyFormatting($sheet, int $row): void
    {
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle("A{$row}:C{$row}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }
}

