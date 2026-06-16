<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\User;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;

class GenerateFeeCard extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.generate-fee-card';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationLabel = 'Generate Fee Card';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public ?int $student_id = null;
    public ?int $class_id = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }

    #[Computed]
    public function students()
    {
        if (!$this->class_id) {
            return collect();
        }

        return User::where('type', 'student')
            ->whereHas('studentClasses', function($query) {
                $query->where('class_id', $this->class_id)
                      ->where('status', 'active');
            })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::orderBy('class_name')->get();
    }

    public function updatedClassId()
    {
        $this->student_id = null;
    }

    public function generate()
    {
        $this->validate([
            'student_id' => 'required|integer',
            'class_id' => 'required|integer',
        ]);

        $student = User::findOrFail($this->student_id);

        return redirect()->route('students.fee-card', [
            'student' => $this->student_id,
        ]);
    }
}
