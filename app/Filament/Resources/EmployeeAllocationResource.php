<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeAllocationResource\Pages;
use App\Filament\Resources\EmployeeAllocationResource\RelationManagers;
use App\Models\EmployeeAllocation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EmployeeAllocationResource extends Resource
{
    protected static ?string $model = EmployeeAllocation::class;

    public static function getLabel(): string
    {
        return __('Employee Allocation');
    }

    public static function getModelLabel(): string
    {
        return __('Employee Allocation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Employee Allocations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Allocations');
    }

    public static function getNavigationGroup(): string
    {
        return __('Inventory Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label(__('Employee'))
                    ->searchable()
                    ->native()
                    ->options(fn() => User::where('type', 'staff')->pluck('name', 'id'))
                    ->required()
                    ->placeholder(__('Select employee')),

                Forms\Components\Select::make('material_id')
                    ->label(__('Material'))
                    ->native(false)
                    ->createOptionForm([
                        // Your MaterialResource form fields here
                        // Example:
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required(),
                        // Add other material fields as needed
                    ])
                    ->relationship('material', 'name')
                    ->required()
                    ->placeholder(__('Select material')),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->placeholder(__('Enter quantity')),

                Forms\Components\DatePicker::make('allocation_date')
                    ->label(__('Allocation Date'))
                    ->jalali()
                    ->locale('fa')
                    ->default(now())
                    ->required()
                    ->placeholder(__('Select allocation date')),

                Forms\Components\DatePicker::make('return_date')
                    ->label(__('Return Date'))
                    ->jalali()
                    ->locale('fa')
                    ->nullable()
                    ->placeholder(__('Select return date (optional)')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn($record) => $record->employee->name ?? __('Not assigned')),

                Tables\Columns\TextColumn::make('material.name')
                    ->label(__('Material'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn($record) => $record->material->name ?? __('Not specified')),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('allocation_date')
                    ->label(__('Allocation Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '')
                    ->sortable()
                    ->tooltip(fn($record) => $record->allocation_date ? Jalalian::fromDateTime($record->allocation_date)->format('Y/m/d') : ''),

                Tables\Columns\TextColumn::make('return_date')
                    ->label(__('Return Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->return_date ? Jalalian::fromDateTime($record->return_date)->format('Y/m/d') : __('Not returned')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip(__('View')),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
                ExportBulkAction::make()
                    ->label(__('Export Selected')),
            ])
            ->emptyStateHeading(__('No allocations found'))
            ->emptyStateDescription(__('Create your first employee allocation'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Allocation')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeAllocations::route('/'),
            'create' => Pages\CreateEmployeeAllocation::route('/create'),
            'view' => Pages\ViewEmployeeAllocation::route('/{record}'),
            'edit' => Pages\EditEmployeeAllocation::route('/{record}/edit'),
        ];
    }
}
