<?php

namespace App\Filament\Resources\InventoryProductResource\Pages;

use App\Filament\Resources\InventoryProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryProduct extends EditRecord
{
    protected static string $resource = InventoryProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('deactivate')
                ->label(__('Deactivate'))
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn(): bool => $this->record->is_active && $this->record->hasInventoryHistory())
                ->action(fn() => $this->record->update(['is_active' => false])),
            Actions\DeleteAction::make()
                ->visible(fn(): bool => ! $this->record->hasInventoryHistory()),
        ];
    }
}
