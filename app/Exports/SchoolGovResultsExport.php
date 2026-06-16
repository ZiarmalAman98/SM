<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SchoolGovResultsExport implements WithEvents
{
    use Exportable;

    /**
     * @param iterable $rows   A Collection|array of your student rows
     *                         Each row can be a model or associative array.
     * @param array    $columnMap  Excel column => data key (or closure) mapping
     * @param int      $startRow   First row to start writing data (default: 7)
     * @param string   $templatePath Full path to the official template .xlsx
     * @param string   $sheetName  Name of the first sheet (default: 'لیست')
     * @param array    $metaCells  Optional: ['E1' => $academic_year, 'H1' => $academic_year - 621, 'F2' => $total_absent_days, ...] to fill header cells
     */
    public function __construct(
        iterable $rows,
        array $columnMap,
        int $startRow,
        string $templatePath,
        string $sheetName = 'لیست',
        array $metaCells = []
    ) {
        $this->rows         = $rows instanceof Collection ? $rows : collect($rows);
        $this->columnMap    = $columnMap;
        $this->startRow     = $startRow;
        $this->templatePath = $templatePath;
        $this->sheetName    = $sheetName;
        $this->metaCells    = $metaCells;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                // 1) Load the original government template
                /** @var Spreadsheet $spreadsheet */
                $spreadsheet = IOFactory::load($this->templatePath);

                // 2) Get the first sheet by name (fallback to index 0 if not found)
                $sheet = $spreadsheet->getSheetByName($this->sheetName)
                      ?: $spreadsheet->getSheet(0);

                // 3) (Optional) Fill metadata/header cells exactly as your template expects
                foreach ($this->metaCells as $cell => $value) {
                    $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                }

                // 4) Write student rows to the first sheet only; other sheets stay intact
                $rowIndex = $this->startRow;

                foreach ($this->rows as $i => $row) {
                    foreach ($this->columnMap as $columnLetter => $keyOrClosure) {
                        // Resolve value from array/model using data_get, or closure
                        $value = is_callable($keyOrClosure)
                            ? $keyOrClosure($row, $i, $rowIndex)
                            : data_get($row, $keyOrClosure);

                        // Use explicit string by default to avoid Excel auto-formatting issues
                        $sheet->setCellValueExplicit(
                            $columnLetter . $rowIndex,
                            $value === null ? '' : $value,
                            DataType::TYPE_STRING
                        );
                    }
                    $rowIndex++;
                }

                // 5) Disable formula pre-calculation to avoid timeout
                $writer = $event->writer->getDelegate();
                if (method_exists($writer, 'setPreCalculateFormulas')) {
                    $writer->setPreCalculateFormulas(false);
                }

                // 6) Replace the writer's spreadsheet
                $writerReflection = new \ReflectionClass($event->writer);
                $spreadsheetProperty = $writerReflection->getProperty('spreadsheet');
                $spreadsheetProperty->setAccessible(true);
                $spreadsheetProperty->setValue($event->writer, $spreadsheet);
            },
        ];
    }
}
