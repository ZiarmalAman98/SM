<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisciplineFormResource\Pages;
use App\Filament\Resources\DisciplineFormResource\RelationManagers;
use App\Models\DisciplineForm;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;

class DisciplineFormResource extends Resource
{
    protected static ?string $model = DisciplineForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    // protected static ?string $navigationLabel = 'Disciplinary Forms';
    // Navigation label (shows in sidebar)
    // protected static ?string $navigationLabel = __('Disciplinary Forms');

    public static function getLabel(): string
    {
        return __('Disciplinary Form');
    }

    public static function getModelLabel(): string
    {
        return __('Disciplinary Form');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Disciplinary Forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('Disciplinary Forms');
    }

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Select::make('student_id')
                        ->label(__('Student'))
                        ->relationship('student', 'id')
                        ->getOptionLabelFromRecordUsing(fn($record) => $record->user->name ?? __('Unknown'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder(__('Select student')),

                    Forms\Components\Select::make('staff_id')
                        ->label(__('Staff'))
                        ->relationship('staff', 'id')
                        ->getOptionLabelFromRecordUsing(fn($record) => $record->user->name ?? __('Unknown'))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder(__('Select staff')),

                    Forms\Components\TextInput::make('reason')
                        ->label(__('Reason'))
                        ->required()
                        ->placeholder(__('Enter reason')),

                    Forms\Components\Select::make('status')
                        ->label(__('Status'))
                        ->native(false)
                        ->options([
                            'warning' => __('Warning'),
                            'notified' => __('Notified'),
                            'suspended' => __('Suspended'),
                        ])
                        ->default('warning')
                        ->required()
                        ->placeholder(__('Select status')),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->fileAttachmentsDirectory('discipline_forms')
                        ->columnSpanFull()
                        ->nullable()
                        ->placeholder(__('Enter description')),

                    Forms\Components\Toggle::make('family_notified')
                        ->label(__('Family Notified'))
                        ->default(false)
                        ->required(),
                ])
                    ->description(__('Discipline Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label(__('Student Name'))
                    ->searchable()
                    ->tooltip(fn($record) => $record->student?->user?->name ?? __('Unknown')),

                Tables\Columns\TextColumn::make('staff.user.name')
                    ->label(__('Staff Name'))
                    ->searchable()
                    ->tooltip(fn($record) => $record->staff?->user?->name ?? __('Unknown')),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('Reason'))
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->reason),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'warning' => 'warning',
                        'notified' => 'info',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\BooleanColumn::make('family_notified')
                    ->label(__('Family Notified'))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime()
                    ->sortable()
                    ->tooltip(fn($record) => $record->created_at->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status Filter'))
                    ->native(false)
                    ->options([
                        'warning' => __('Warning'),
                        'notified' => __('Notified'),
                        'suspended' => __('Suspended'),
                    ])
                    ->placeholder(__('All Statuses')),
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
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
            ])
            ->emptyStateHeading(__('No disciplinary records found'))
            ->emptyStateDescription(__('Create your first disciplinary record'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Record')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisciplineForms::route('/'),
            'create' => Pages\CreateDisciplineForm::route('/create'),
            'edit' => Pages\EditDisciplineForm::route('/{record}/edit'),
        ];
    }
}
