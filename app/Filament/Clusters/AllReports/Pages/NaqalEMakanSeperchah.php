<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Actions;
use Filament\Pages\Page;
use App\Models\Branch;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use Livewire\Attributes\Computed;

class NaqalEMakanSeperchah extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.naqal-e-makan-seperchah';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    public static function getNavigationLabel(): string
    {
        return __('Naqal E Makan Seperchah');
    }
    
    protected static ?int $navigationSort = 5;

    public ?int $branch_id = null;
    public ?int $class_id = null;
    public ?string $academic_year = null;
    public ?int $student_id = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate')
                ->label(__('Generate Report'))
                ->action('generate')
                ->icon('heroicon-o-printer')
                ->color('primary'),
        ];
    }

    public function mount(): void
    {
        // Set default academic year to current year if needed
        $this->academic_year = $this->academic_year ?? now()->year;
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label(__('Branch'))
                    ->options(fn() => $this->branches->pluck('branch_name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->class_id = null),
                    
                Forms\Components\Select::make('class_id')
                    ->label(__('Class'))
                    ->options(fn() => $this->classes->pluck('class_name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->student_id = null),
                    
                Forms\Components\Select::make('academic_year')
                    ->label(__('Academic Year'))
                    ->options(fn() => $this->academicYears->mapWithKeys(fn($year) => [$year => $year]))
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->student_id = null),
                    
                Forms\Components\Select::make('student_id')
                    ->label(__('Student'))
                    ->options(fn() => $this->students->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
            ])
            ->statePath('data');
    }

    #[Computed]
    public function branches()
    {
        return Branch::orderBy('branch_name')->get();
    }

    #[Computed]
    public function classes()
    {
        if (!$this->branch_id) {
            return collect();
        }

        return SchoolClass::where('branch_id', $this->branch_id)
            ->orderBy('class_name')
            ->get();
    }

    #[Computed]
    public function academicYears()
    {
        return StudentClass::query()
            ->whereNotNull('academic_year')
            ->select('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');
    }

    #[Computed]
    public function students()
    {
        if (!$this->class_id) {
            return collect();
        }

        return User::where('type', 'student')
            ->whereHas('studentClasses', function ($q) {
                $q->where('class_id', $this->class_id)
                  ->when($this->academic_year, fn($qq) => $qq->where('academic_year', $this->academic_year))
                  ->where('status', 'active');
            })
            ->orderBy('name')
            ->get();
    }

    public function generate()
    {
        $this->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'class_id' => 'required|integer|exists:school_classes,id',
            'academic_year' => 'nullable|string',
            'student_id' => 'required|integer|exists:users,id',
        ]);

        // You'll need to create this route and the corresponding print view
        return redirect()->route('naqal-e-makan.print', [
            'branch_id' => $this->branch_id,
            'class_id' => $this->class_id,
            'academic_year' => $this->academic_year,
            'student_id' => $this->student_id,
        ]);
    }

    // Alternative method if you want to display the report directly on this page
    public function generateReport()
    {
        $this->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'class_id' => 'required|integer|exists:school_classes,id',
            'academic_year' => 'nullable|string',
            'student_id' => 'required|integer|exists:users,id',
        ]);

        // Get student data for the report
        $student = User::with(['studentClasses', 'addresses', 'branch'])
            ->find($this->student_id);

        // You can pass this data to your view
        return view('reports.naqal-e-makan', compact('student'));
    }
}