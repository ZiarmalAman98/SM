<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Actions;
use Filament\Pages\Page;
use App\Models\Biography;
use Livewire\Attributes\Computed;

class CartSwaneh extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.cart-swaneh';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Cart Swaneh';

    protected static ?int $navigationSort = 2;

    public ?int $biography_id = null;

    public function mount(): void
    {
        // This page will be accessed via the ReportsCenter action
    }

    public function generateCard(): void
    {
        $this->validate([
            'biography_id' => 'required|exists:biographies,id',
        ]);

        $biography = Biography::findOrFail($this->biography_id);
        $this->redirect(route('biographies.card', $biography), navigate: false);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
