<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;
    protected static ?int $navigationSort = 2;

    public static function getLabel(): string
    {
        return __('Income');
    }

    public static function getModelLabel(): string
    {
        return __('Income');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Incomes');
    }

    public static function getNavigationLabel(): string
    {
        return __('Incomes');
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
                        Forms\Components\Select::make('source_id')
                            ->label(__('Source'))
                            ->relationship('source', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('Select an income source')),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->required()
                            ->numeric()
                            ->prefix("AF")
                            ->placeholder(__('Enter the amount')),

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

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull()
                            ->placeholder(__('Add a description if necessary')),

                        Forms\Components\FileUpload::make('attachment')
                            ->label(__('Attachment'))
                            ->directory('uploads/income_attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(4096)
                            ->columnSpanFull()
                            ->placeholder(__('Attach a relevant file')),
                    ])
                    ->description(__('Income Form'))
                    ->collapsed(false)
                    ->columns(2)
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source.name')
                    ->label(__('Source'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn($record) => $record->source->name ?? __('Not specified')),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn($state) => 'AF ' . number_format($state, 2))
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->html()
                    ->limit(50)
                    ->tooltip(fn($record) => strip_tags($record->description))
                    ->toggleable()
                    ->default(__('N/A')),

                Tables\Columns\TextColumn::make('attachment')
                    ->label(__('Attachment'))
                    ->url(fn($record) => $record->attachment ? url('storage/' . $record->attachment) : null)
                    ->openUrlInNewTab()
                    ->toggleable()
                    ->default(__('No Attachment')),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                    ])
                    ->native(false)
                    ->placeholder(__('All Statuses')),

                Tables\Filters\Filter::make('date')
                    ->label(__('Income Date'))
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
                                $data['from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date)
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
            ->emptyStateHeading(__('No incomes found'))
            ->emptyStateDescription(__('Create your first income record'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Income')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'view' => Pages\ViewIncome::route('/{record}'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
