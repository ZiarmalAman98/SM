<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchoolGovResultsExport;
use Morilog\Jalali\Jalalian;
use App\Models\Subject;


class ResultsExcelTable extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.clusters.all-reports.pages.results-excel-table';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationLabel = 'Export Class Exam Result';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('Branch')
                    ->options(fn() => \App\Models\Branch::pluck('branch_name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('class_id')
                    ->label('Class')
                    ->options(function (callable $get) {
                        $branchId = $get('branch_id');
                        if (!$branchId) return [];
                        return \App\Models\SchoolClass::where('branch_id', $branchId)
                            ->pluck('class_name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('academic_year')
                    ->label('Academic Year (Jalali)')
                    ->placeholder('1404')
                    ->numeric()
                    ->minValue(1400)
                    ->maxValue(1450)
                    ->default(Jalalian::now()->getYear())
                    ->required()
                    ->afterStateUpdated(function ($state, $set) {
                        // Convert Jalali year to Gregorian
                        if ($state && is_numeric($state)) {
                            $gregorianYear = (int)$state + 621;
                            $set('academic_year_gregorian', $gregorianYear);
                        }
                    }),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('export')
                        ->label('Export Results')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action('exportResults')
                ])
            ])
            ->statePath('data');
    }

    public function exportResults(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = $this->form->getState();

        // Convert Jalali year to Gregorian for database filtering
        $gregorianYear = (int)$data['academic_year'] + 621;
        $data['academic_year'] = $gregorianYear;

        set_time_limit(50000);
        ini_set('memory_limit', '1G');

        // Get students using whereHas with both class_id and academic_year
        // $students = \App\Models\Student::whereHas('studentClasses', function ($query) use ($data) {
        //         $query->where('class_id', $data['class_id'])
        //               ->where('academic_year', $data['academic_year']);
        //     })
        //     ->limit(50)
        //     ->get();

        $students = \App\Models\Student::select(['id', 'user_id', 'roll_no', 'admission_no', 'dob', 'gender', 'grand_father_name', 'tazkira_number'])
            ->with([
                'user:id,name,father_name',
                'user.exam_scores' => function($query) use ($data) {
                    $query->with(['exam', 'subject:id,name'])
                          ->where('class_id', $data['class_id'])
                          ->whereHas('exam', function($examQuery) use ($data) {
                              $examQuery->whereYear('date', $data['academic_year']);
                          });
                }
            ])
            ->whereHas('studentClasses', function ($query) use ($data) {
                $query->where('class_id', $data['class_id']);
            })
            ->limit(50)
            ->get();

        // dd("students", $students);



        // Get subjects for the selected class
        $class = \App\Models\SchoolClass::find($data['class_id']);
        $subjects = Subject::where('school_class_id', $data['class_id'])
            ->orderBy('id')
            ->pluck('name')
            ->toArray();

        // dd($subjects);

        // Now exam scores are filtered by the selected class_id



        $columnMap = [
            'A' => fn($student, $i) => $i + 1,
            'B' => 'user.name',
            'C' => 'user.father_name',
            'D' => 'grand_father_name',
            'E' => 'roll_no',
            'F' => 'tazkira_number',
            'G' => '',
            'H' => '',
            'I' => '',
            'J' => ' ',
            'AE' => ' ',
        ];

        /* ---------------- MID-TERM SUBJECTS (start at K) ---------------- */
        $columns = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
        $columnIndex = 0;

        // Fill mid-term subjects
        foreach ($subjects as $index => $subject) {
            if ($columnIndex < count($columns)) {
                $columnMap[$columns[$columnIndex]] = fn($s) => $s->getMidTermMark($subject);
                $columnIndex++;
            }
        }

        // Fill remaining cells up to W with empty values
        while ($columnIndex < count($columns)) {
            $columnMap[$columns[$columnIndex]] = '';
            $columnIndex++;
        }

        /* --- Add 3 empty gap columns before attendance --- */
        $columnMap['X'] = '';
        $columnMap['Y'] = '';
        $columnMap['Z'] = '';

        /* ---------------- MID-TERM ATTENDANCE (4 columns) ---------------- */
        $columnMap['AA'] = fn($s) => $s->getMidTermAttendance(1); // Present
        $columnMap['AB'] = fn($s) => $s->getMidTermAttendance(0); // Absent
        $columnMap['AC'] = fn($s) => $s->getMidTermAttendance(2); // Leave
        $columnMap['AD'] = fn($s) => $s->getMidTermAttendance(3); // Sick


        /* ---------------- FINAL SUBJECTS (start at AF) ---------------- */
        $finalColumns = ['AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR'];
        $finalColumnIndex = 0;

        // Fill final-term subjects
        foreach ($subjects as $index => $subject) {
            if ($finalColumnIndex < count($finalColumns)) {
                $columnMap[$finalColumns[$finalColumnIndex]] = fn($s) => $s->getFinalMark($subject);
                $finalColumnIndex++;
            }
        }

        // Fill remaining cells up to AR with empty values
        while ($finalColumnIndex < count($finalColumns)) {
            $columnMap[$finalColumns[$finalColumnIndex]] = '';
            $finalColumnIndex++;
        }

        /* --- Add 3 empty gap columns before attendance --- */
        $columnMap['AS'] = '';
        $columnMap['AT'] = '';
        $columnMap['AU'] = '';

        /* ---------------- FINAL ATTENDANCE (4 columns) ---------------- */
        $columnMap['AV'] = fn($s) => $s->getFinalAttendance(1); // Present
        $columnMap['AW'] = fn($s) => $s->getFinalAttendance(0); // Absent
        $columnMap['AX'] = fn($s) => $s->getFinalAttendance(2); // Leave
        $columnMap['AY'] = fn($s) => $s->getFinalAttendance(3); // Sick

        // subjects and attendace








        $jalaliDate = Jalalian::now();

        // 🧮 Calculate total absents filtered by branch, class, and academic year
        $totalAbsents = \App\Models\ClassAttendance::query()
            ->where('branch_id', $data['branch_id'])
            ->where('class_id', $data['class_id'])
            ->whereYear('date', $data['academic_year'])
            ->where('status', 0) // 0 = absent
            ->count();


        $metaCells = [
            'E1' => $data['academic_year'],            // Gregorian year
            'H1' => $data['academic_year'] - 621,      // Jalali year (approx)
            'F2' => $totalAbsents,                     // ✅ total absents for selected branch/class/year
        ];


        // Add subject names to metaCells for row 3 headers
        $subjectHeaders = [];
        $columns = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
        $finalColumns = ['AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR'];

        // Add mid-term subject headers
        foreach ($subjects as $index => $subject) {
            if ($index < count($columns)) {
                $subjectHeaders[$columns[$index] . '3'] = $subject;
            }
        }

        // Add final subject headers
        foreach ($subjects as $index => $subject) {
            if ($index < count($finalColumns)) {
                $subjectHeaders[$finalColumns[$index] . '3'] = $subject;
            }
        }

        // Merge with existing metaCells
        $allMetaCells = array_merge($metaCells, $subjectHeaders);

        return Excel::download(
            new SchoolGovResultsExport(
                $students,
                $columnMap,
                5, // Start data from row 5, headers in row 3
                storage_path('app/templates/results_template.xlsx'),
                'لیست',
                $allMetaCells
            ),
            "school-results-{$data['branch_id']}-{$data['class_id']}-" . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }
}
