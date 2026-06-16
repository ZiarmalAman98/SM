<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms;

class ListIncomes extends ListRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Print Incomes')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->required()
                        ->label('From Date'),

                    Forms\Components\DatePicker::make('to')
                        ->required()
                        ->label('To Date'),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->searchable()
                        ->preload()
                        ->placeholder('All Statuses'),
                ])
                ->action(function (array $data) {
                    // Redirect to custom route with query params
                    return redirect()->route('income.print', [
                        'from' => $data['from'],
                        'to' => $data['to'],
                        'status' => $data['status'],
                    ]);
                })
                ->modalHeading('Print Income Report')
                ->modalSubmitActionLabel('Generate'),
        ];
    }
}
