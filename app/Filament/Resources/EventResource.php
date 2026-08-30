<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return __('Event');
    }

    public static function getModelLabel(): string
    {
        return __('Event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    public static function getNavigationLabel(): string
    {
        return __('Events');
    }

    public static function getNavigationGroup(): string
    {
        return __('Calendar');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('Enter event title')),

                        Forms\Components\ColorPicker::make('color')
                            ->label(__('Color'))
                            ->required()
                            ->default('#3b82f6'),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label(__('Start Date & Time'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now())
                            ->required()
                            ->placeholder(__('Select start date')),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label(__('End Date & Time'))
                            ->jalali()
                            ->locale('fa')
                            ->required()
                            ->placeholder(__('Select end date'))
                            ->rules([
                                fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('starts_at') > $value) {
                                        $fail(__('End date must be after start date'));
                                    }
                                },
                            ]),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->fileAttachmentsDirectory('events')
                            ->columnSpanFull()
                            ->placeholder(__('Enter event description')),
                    ])
                    ->description(__('Event Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ColorColumn::make('color')
                    ->label(__('Color'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label(__('Start Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->starts_at->diffForHumans()),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label(__('End Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->ends_at->diffForHumans()),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label(__('Start Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => \Carbon\Carbon::parse($record->starts_at)->diffForHumans()),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label(__('End Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => \Carbon\Carbon::parse($record->ends_at)->diffForHumans()),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                // Add date range filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                ]),
            ])
            ->emptyStateHeading(__('No events found'))
            ->emptyStateDescription(__('Create your first event'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Event')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
