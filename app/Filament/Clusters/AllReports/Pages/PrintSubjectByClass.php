<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;

class PrintSubjectByClass extends Page
{
	protected static string $view = 'filament.clusters.all-reports.pages.print-subject-by-class';

	protected static ?string $cluster = AllReports::class;

	protected static ?string $navigationLabel = 'Print Shuqa';
	protected static ?string $navigationIcon = 'heroicon-o-printer';

	public ?int $branch_id = null;
	public ?int $class_id = null;
	public ?int $subject_id = null;
	public ?string $exam_type = null;

	protected function getHeaderActions(): array
	{
		return [];
	}

	public function getHeaderWidgets(): array
	{
		return [];
	}

	public function updatedBranchId()
	{
		$this->class_id = null;
		$this->subject_id = null;
	}

	public function updatedClassId()
	{
		$this->subject_id = null;
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
	public function subjects()
	{
		if (!$this->class_id) {
			return collect();
		}

		return \App\Models\Subject::whereHas('schoolClass', fn($q) => $q->where('school_class_id', $this->class_id))
			->orderBy('name')
			->get();
	}

	public function generate()
	{
		$this->validate([
			'branch_id' => 'required|integer',
			'class_id' => 'required|integer',
			'subject_id' => 'required|integer',
			'exam_type' => 'required|string',
		]);

		return redirect()->route('examResults.subject.print', [
			'branch_id'  => $this->branch_id,
			'class_id'   => $this->class_id,
			'subject_id' => $this->subject_id,
			'exam_type'  => $this->exam_type,
		]);
	}
}


