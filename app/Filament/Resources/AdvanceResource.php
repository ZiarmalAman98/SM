<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvanceResource\Pages;
use App\Filament\Resources\AdvanceResource\RelationManagers;
use App\Models\Advance;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;  
use Morilog\Jalali\Jalalian;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AdvanceResource extends Resource
{
    protected static ?string $model = Advance::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }

    public static function getLabel(): string
    {
        return __('Advance');
    }

    public static function getModelLabel(): string
    {
        return __('Advance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Advances');
    }

    public static function getNavigationLabel(): string
    {
        return __('Advances');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
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
                        ->label(__('Advance Amount')),

                    Forms\Components\DatePicker::make('date')
                        ->jalali()          // keep Jalali calendar
                        ->locale('fa')      // still "fa", we patched its labels
                        ->default(Carbon::now())
                        ->required()
                        ->label(__('Date')),



                    Forms\Components\RichEditor::make('reason')
                        ->nullable()
                        ->fileAttachmentsDirectory('advance-reasons')
                        ->columnSpanFull()
                        ->label(__('Reason for Advance')),
                ])
                    ->description(__("Advance Form"))
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
                    ->formatStateUsing(function ($state) {
                        try {
                            return $state ? Jalalian::fromCarbon(Carbon::parse($state))->format('Y/m/d') : '';
                        } catch (\Exception $e) {
                            return $state; // Fallback to original value if parsing fails
                        }
                    })
                    ->sortable()
                    ->label(__('Date')),

                Tables\Columns\TextColumn::make('reason')
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->default(__('N/A'))
                    ->label(__('Reason')),

                Tables\Columns\TextColumn::make('created_at')
                    ->formatStateUsing(function ($state) {
                        try {
                            return $state ? Jalalian::fromCarbon(Carbon::parse($state))->format('Y/m/d H:i') : '';
                        } catch (\Exception $e) {
                            return $state;
                        }
                    })
                    ->sortable()
                    ->label(__('Created At')),
            ])
            ->filters([
                Tables\Filters\Filter::make('Recent Advances')
                    ->label(__('Recent Advances'))
                    ->query(fn($query) => $query->where('date', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn (Advance $record) => route('advances.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                    ExportBulkAction::make()
                        ->label(__('Export Selected'))
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvances::route('/'),
            'create' => Pages\CreateAdvance::route('/create'),
            'edit' => Pages\EditAdvance::route('/{record}/edit'),
        ];
    }
}
