<?php

namespace App\Filament\Resources;

use App\Models\Deduction;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\DeductionResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DeductionResource extends Resource
{
    protected static ?string $model = Deduction::class;  
    protected static ?string $navigationIcon = 'heroicon-o-document-minus';
    protected static ?int $navigationSort = 5;

    public static function getLabel(): string
    {
        return __('Deduction');
    }

    public static function getModelLabel(): string
    {
        return __('Deduction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Deductions');
    }

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('Deductions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship(
                                'user',
                                'name',
                                fn (Builder $query) => $query->whereIn('type', ['staff', 'teacher']),
                            )
                            ->searchable()
                            ->required()
                            ->preload()
                            ->label(__('User')),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->default(0)
                            ->label(__('Deduction Amount')),

                        Forms\Components\DatePicker::make('date')
                            ->jalali()
                            ->locale('fa')
                            ->required()
                            ->default(now())
                            ->label(__('Date'))
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('reason')
                            ->nullable()
                            ->fileAttachmentsDirectory('deduction-reasons')
                            ->columnSpanFull()
                            ->label(__('Reason for Deduction')),
                    ])
                    ->description(__('Deduction Form'))
                    ->collapsed(false)
                    ->columns(2)
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('User')),

                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->label(__('Amount')),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d')),

                Tables\Columns\TextColumn::make('reason')
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->default(__('N/A'))
                    ->label(__('Reason')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i')),
            ])
            ->filters([
                Tables\Filters\Filter::make('Recent Deductions')
                    ->label(__('Recent Deductions'))
                    ->query(fn($query) => $query->where('date', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),

                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),

                    ExportBulkAction::make()
                        ->label(__('Export Selected')),
                ]),
            ])
            ->emptyStateHeading(__('No deductions found'))
            ->emptyStateDescription(__('Create your first deduction record'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Deduction')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeductions::route('/'),
            'create' => Pages\CreateDeduction::route('/create'),
            'edit' => Pages\EditDeduction::route('/{record}/edit'),
        ];
    }
}
