<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CreateExcelTemplate extends Command
{
    protected $signature = 'excel:create-template';
    protected $description = 'Create Excel template for formatted results';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'A1' => 'Student ID',
            'B1' => 'Student Name',
            'C1' => 'Father Name',
            'D1' => 'Grandfather Name',
            'E1' => 'Subject',
            'F1' => 'Mid Term',
            'G1' => 'Final',
            'H1' => 'Total',
            'I1' => 'Written Marks',
            'J1' => 'Recital Marks',
            'K1' => 'Homework Marks',
            'L1' => 'Activity Marks',
            'M1' => 'Mark in Words',
        ];

        foreach ($headers as $cell => $value) {
            $worksheet->setCellValue($cell, $value);
        }

        // Style the header row
        $worksheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2F5496'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Set column widths
        $columnWidths = [
            'A' => 12, // Student ID
            'B' => 20, // Student Name
            'C' => 20, // Father Name
            'D' => 20, // Grandfather Name
            'E' => 15, // Subject
            'F' => 12, // Mid Term
            'G' => 12, // Final
            'H' => 12, // Total
            'I' => 15, // Written Marks
            'J' => 15, // Recital Marks
            'K' => 15, // Homework Marks
            'L' => 15, // Activity Marks
            'M' => 20, // Mark in Words
        ];

        foreach ($columnWidths as $column => $width) {
            $worksheet->getColumnDimension($column)->setWidth($width);
        }

        // Add some sample data
        $sampleData = [
            ['1', 'Ahmad Ali', 'Mohammad', 'Hassan', 'Mathematics', '85', '90', '175', '40', '25', '30', '20', 'One Hundred Seventy Five'],
            ['2', 'Fatima Zahra', 'Ali', 'Hussein', 'English', '78', '82', '160', '35', '20', '25', '18', 'One Hundred Sixty'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $worksheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Apply borders to all data
        $worksheet->getStyle('A1:M' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Save the template
        $writer = new Xlsx($spreadsheet);
        $templatePath = public_path('documents/result.xlsx');
        $writer->save($templatePath);

        $this->info("Excel template created successfully at: {$templatePath}");
    }
}

