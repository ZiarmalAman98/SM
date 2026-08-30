<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?int $navigationSort = 4;

    public static function getLabel(): string
    {
        return __('Expense');
    }

    public static function getModelLabel(): string
    {
        return __('Expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationGroup(): string
    {
        return __('Finance');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->placeholder(__('Select a category')),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->required()
                            ->numeric()
                            ->prefix('AF')
                            ->placeholder(__('Enter amount')),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now())
                            ->required()
                            ->placeholder(__('Select date')),

                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->native(false)
                            ->options([
                                'pending' => __('Pending'),
                                'approved' => __('Approved'),
                                'rejected' => __('Rejected'),
                            ])
                            ->default('pending')
                            ->required()
                            ->placeholder(__('Select status')),

                        Forms\Components\TextInput::make('invoice_no')
                            ->label(__('Invoice Number'))
                            ->unique(ignoreRecord: true)
                            ->placeholder(__('Enter invoice number'))
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('Enter description'))
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('invoice')
                            ->label(__('Invoice'))
                            ->directory('uploads/expenses')
                            ->placeholder(__('Upload invoice'))
                            ->columnSpanFull()
                            ->acceptedFileTypes(['application/pdf', 'image/*']),
                    ])
                    ->description(__('Expense Form'))
                    ->collapsed(false)
                    ->columns(2),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label(__('Invoice No'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->sortable()
                    ->prefix('AF '),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice')
                    ->label(__('Invoice'))
                    ->url(fn($record) => $record->invoice ? url('storage/' . $record->invoice) : null)
                    ->default(__('No Invoice Uploaded')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __($state))
                    ->color(fn($state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),

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
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->placeholder(__('All categories')),

                Filter::make('date')
                    ->label(__('Expense Date'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('From'))
                            ->jalali()
                            ->locale('fa'),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('Until'))
                            ->jalali()
                            ->locale('fa'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['from'] && $data['until']) {
                            $from = Jalalian::fromDateTime($data['from'])->format('Y/m/d');
                            $until = Jalalian::fromDateTime($data['until'])->format('Y/m/d');
                            return __('From :from to :until', ['from' => $from, 'until' => $until]);
                        }

                        if ($data['from']) {
                            $from = Jalalian::fromDateTime($data['from'])->format('Y/m/d');
                            return __('From :date', ['date' => $from]);
                        }

                        if ($data['until']) {
                            $until = Jalalian::fromDateTime($data['until'])->format('Y/m/d');
                            return __('Until :date', ['date' => $until]);
                        }

                        return null;
                    }),
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
            ->emptyStateHeading(__('No expenses found'))
            ->emptyStateDescription(__('Create your first expense'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Expense')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
