<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;

class PrintExamResult extends Page
{
	protected static string $view = 'filament.clusters.all-reports.pages.print-exam-result';

	protected static ?string $cluster = AllReports::class;

	protected static ?string $navigationLabel = 'Print Etlā Nāmah';
	protected static ?string $navigationIcon = 'heroicon-o-printer';

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
		return \App\Models\User::where('type', 'student')
			->orderBy('name')
			->get();
	}

	#[Computed]
	public function classes()
	{
		return SchoolClass::orderBy('class_name')->get();
	}

	public function generate()
	{
		$this->validate([
			'student_id' => 'required|integer',
			'class_id' => 'required|integer',
		]);

		return redirect()->route('examResult.print', [
			'student_id' => $this->student_id,
			'class_id'   => $this->class_id,
		]);
	}
}


