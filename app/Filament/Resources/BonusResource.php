<?php

namespace App\Filament\Resources;

use App\Models\Bonus;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\BonusResource\Pages;

class BonusResource extends Resource
{
    protected static ?string $model = Bonus::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Bonus');
    }

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bonuses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bonuses');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                        ->label(__('User'))
                        ->placeholder(__('Select User')),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->step(0.01)
                        ->required()
                        ->default(0)
                        ->label(__('Bonus Amount'))
                        ->placeholder(__('Enter amount')),

                    Forms\Components\DatePicker::make('date')
                        ->jalali()
                        ->locale('fa')
                        ->required()
                        ->default(now())
                        ->label(__('Date'))
                        ->placeholder(__('Select date'))
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('reason')
                        ->nullable()
                        ->fileAttachmentsDirectory('bonus-reasons')
                        ->columnSpanFull()
                        ->label(__('Reason for Bonus'))
                        ->placeholder(__('Enter reason')),
                ])
                ->description(__('Bonus Form'))
                ->collapsed(false)
                ->columns(2)
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('User'))
                    ->formatStateUsing(fn($state, $record): string => $record->user?->name ?? __('No User'))
                    ->tooltip(fn($record): string => $record->user?->name ?? __('No User')),

                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->label(__('Amount'))
                    ->formatStateUsing(fn($state): string => number_format($state, 2)),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->sortable()
                    ->formatStateUsing(fn($state): string => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->tooltip(fn($record): string => Jalalian::fromDateTime($record->date)->format('l، j F Y')),

                Tables\Columns\TextColumn::make('reason')
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->default(__('N/A'))
                    ->label(__('Reason'))
                    ->formatStateUsing(fn($state): string => $state ?: __('N/A'))
                    ->tooltip(fn($record): string => $record->reason ? strip_tags($record->reason) : __('N/A')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state): string => Jalalian::fromDateTime($state)->format('Y/m/d H:i')),
            ])
            ->filters([
                Tables\Filters\Filter::make('Recent Bonuses')
                    ->label(__('Recent Bonuses (Last Month)'))
                    ->query(fn(Builder $query): Builder => $query->where('date', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('')->tooltip(__('Edit')),
                Tables\Actions\DeleteAction::make()->label('')->tooltip(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
                    ExportBulkAction::make()->label(__('Export Selected')),
                ]),
            ])
            ->emptyStateHeading(__('No bonuses found'))
            ->emptyStateDescription(__('Create your first bonus record'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label(__('Create Bonus')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBonuses::route('/'),
            'create' => Pages\CreateBonus::route('/create'),
            'edit' => Pages\EditBonus::route('/{record}/edit'),
        ];
    }
}
