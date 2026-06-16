<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Computed;

class ExportStudentLastYear extends Page implements HasForms
{
	use InteractsWithForms;

	protected static string $view = 'filament.clusters.all-reports.pages.export-student-last-year';

	protected static ?string $cluster = AllReports::class;

	protected static ?string $navigationLabel = 'Qesmat-kunanda Seh Parcha';
	protected static ?string $navigationIcon = 'heroicon-m-arrow-down-tray';

	public ?array $data = [];

	public function mount(): void
	{
		$this->form->fill([
			'academic_year' => \Morilog\Jalali\Jalalian::fromCarbon(now())->getYear() - 1,
		]);
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

						$studentIds = \App\Models\StudentClass::where('class_id', $classId)
							->where('status', 'active')
							->pluck('student_id');

						return \App\Models\User::whereIn('id', $studentIds)
							->pluck('name', 'id')
							->toArray();
					})
					->searchable()
					->preload()
					->required(),

				Forms\Components\Select::make('academic_year')
					->label('Academic Year (Hijri Shamsi)')
					->options(function() {
						$currentYear = \Morilog\Jalali\Jalalian::fromCarbon(now())->getYear();
						$years = [];
						for ($i = 0; $i < 5; $i++) {
							$year = $currentYear - $i;
							$years[$year] = $year . ' (هجری شمسی)';
						}
						return $years;
					})
					->default(\Morilog\Jalali\Jalalian::fromCarbon(now())->getYear() - 1)
					->required(),

				Forms\Components\Actions::make([
					Forms\Components\Actions\Action::make('export')
						->label('Export Student Result')
						->icon('heroicon-o-document-arrow-down')
						->color('primary')
						->action('exportStudentResult')
				])
			])
			->columns(2)
			->statePath('data');
	}

	public function exportStudentResult(): \Symfony\Component\HttpFoundation\BinaryFileResponse
	{
		$data = $this->form->getState();

		return (new \App\Exports\StudentYearResultExport($data['student_id'], $data['academic_year']))
			->download("student-{$data['student_id']}-{$data['academic_year']}.xlsx");
	}
}


