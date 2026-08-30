<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Filament\Resources\ExamResource\RelationManagers\ResultsRelationManager;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Morilog\Jalali\Jalalian;
use Filament\Forms\Components\Section;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;

    public static function getLabel(): string
    {
        return __('Exam');
    }

    public static function getModelLabel(): string
    {
        return __('Exam');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Exams');
    }

    public static function getNavigationLabel(): string
    {
        return __('Exams Schedule');
    }

    public static function getNavigationGroup(): string
    {
        return __('Examinations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Exam Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('Enter exam name')),

                        Forms\Components\Select::make('class_id')
                            ->relationship('class', 'class_name')
                            ->label(__('Class'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('Select class')),

                        Forms\Components\Select::make('exam_type')
                            ->label(__('Exam Type'))
                            ->options([
                                'mid_term' => __('Mid-term Exam'),
                                'final' => __('Final Exam'),
                            ])
                            ->required()
                            ->searchable()
                            ->placeholder(__('Select exam type')),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('Exam Date'))
                            ->jalali()
                            ->locale('fa')
                            ->required()
                            ->placeholder(__('Select exam date')),

                        Forms\Components\TimePicker::make('time')
                            ->label(__('Exam Time'))
                            ->nullable()
                            ->placeholder(__('Select exam time')),
                    ])
                    ->description(__('Exam Form'))
                    ->collapsed(false)
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Exam Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('class.class_name')
                    ->label(__('Class'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('exam_type')
                    ->label(__('Exam Type'))
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'mid_term' => __('Mid-term Exam'),
                            'final' => __('Final Exam'),
                            default => __('Unknown'),
                        };
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'mid_term' => 'info',
                            'final' => 'success',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('time')
                    ->label(__('Time'))
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __($state))
                    ->color(fn($state) => $state === 'Due' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->created_at->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label(__('Class'))
                    ->searchable()
                    ->preload()
                    ->relationship('class', 'class_name')
                    ->placeholder(__('All classes')),

                Filter::make('exam_date')
                    ->label(__('Exam Date'))
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

                Filter::make('exam_time')
                    ->label(__('Exam Time'))
                    ->form([
                        Forms\Components\TimePicker::make('from')->label(__('From Time')),
                        Forms\Components\TimePicker::make('until')->label(__('Until Time')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $time): Builder => $query->where('time', '>=', $time),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $time): Builder => $query->where('time', '<=', $time),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['from'] && $data['until']) {
                            return __('Time: :from – :until', ['from' => $data['from']->format('H:i'), 'until' => $data['until']->format('H:i')]);
                        }

                        if ($data['from']) {
                            return __('Time after :time', ['time' => $data['from']->format('H:i')]);
                        }

                        if ($data['until']) {
                            return __('Time before :time', ['time' => $data['until']->format('H:i')]);
                        }

                        return null;
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
            ])
            ->emptyStateHeading(__('No exams found'))
            ->emptyStateDescription(__('Create your first exam'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Exam')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
