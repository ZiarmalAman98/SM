<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class AssuranceLetter extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.clusters.all-reports.pages.assurance-letter';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationLabel = 'Maktob Etmenaniya';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';

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
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('student_id')
                    ->label('Student')
                    ->options(function (callable $get) {
                        $classId = $get('class_id');
                        if (!$classId) return [];
                        return \App\Models\Student::whereHas('studentClasses', function ($query) use ($classId) {
                            $query->where('class_id', $classId);
                        })->with('user')->get()->pluck('user.name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('academic_year')
                    ->label('Academic Year')
                    ->options([
                        '2025' => '2025',
                        '2024' => '2024',
                        '2023' => '2023',
                        '2022' => '2022',
                    ])
                    ->default('2025')
                    ->required(),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('generate')
                        ->label('Generate Letter')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action('generateLetter')
                ])
            ])
            ->statePath('data');
    }

    public function generateLetter()
    {
        $data = $this->form->getState();

        $student = \App\Models\Student::with(['user', 'studentClasses.schoolClass'])
            ->find($data['student_id']);

        if (!$student) {
            $this->addError('student_id', 'Student not found.');
            return;
        }

        // Redirect to template
        return redirect()->route('assurance-letter.template', [
            'student' => $student->id,
            'branch' => $data['branch_id'],
            'class' => $data['class_id'],
            'year' => $data['academic_year']
        ]);
    }
}
