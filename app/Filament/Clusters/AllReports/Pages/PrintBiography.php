<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Actions;
use Filament\Pages\Page;
use App\Models\Biography;
use Livewire\Attributes\Computed;

class PrintBiography extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.print-biography';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Print Biography';

    protected static ?int $navigationSort = 1;

    public ?int $biography_id = null;

    public function mount(): void
    {
        // This page will be accessed via the ReportsCenter action
    }

    public function printBiography(): void
    {
        $this->validate([
            'biography_id' => 'required|exists:biographies,id',
        ]);

        $biography = Biography::findOrFail($this->biography_id);
        $this->redirect(route('biographies.print', $biography), navigate: false);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
