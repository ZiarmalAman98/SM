<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\EmployeeSalaryResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeSalaryResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getPluralModelLabel(): string
    {
        return __('My Salaries');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(
                fn(Builder $query) => $query->whereHas(
                    'payroll',
                    fn($q) => $q->where('teacher_id', auth()->id())
                )
            )
            ->columns([
                Tables\Columns\TextColumn::make('payroll.payroll_number')
                    ->label(__('Payroll Number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payroll.base_salary')
                    ->label(__('Base Salary'))
                    ->numeric()
                    ->sortable()
                    ->money('AFN'), // Added currency formatting

                Tables\Columns\TextColumn::make('payroll.bonus')
                    ->label(__('Bonus'))
                    ->numeric()
                    ->sortable()
                    ->money('AFN'),

                Tables\Columns\TextColumn::make('payroll.deductions')
                    ->label(__('Deductions'))
                    ->numeric()
                    ->sortable()
                    ->money('AFN'),

                Tables\Columns\TextColumn::make('payroll.net_salary')
                    ->label(__('Net Salary'))
                    ->numeric()
                    ->sortable()
                    ->money('AFN'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount Paid'))
                    ->numeric()
                    ->sortable()
                    ->money('AFN'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __($state))
                    ->getStateUsing(fn($record) => $record->amount >= $record->payroll->net_salary ? 'paid' : 'pending')
                    ->colors([
                        'paid' => 'success',
                        'pending' => 'warning',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSalaries::route('/'),
        ];
    }
}