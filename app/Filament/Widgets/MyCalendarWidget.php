<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\EventResource;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;

class MyCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Event::class;

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Calendar');
    }

    public function fetchEvents(array $fetchInfo): array
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin']), 403);

        return Event::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn(Event $event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'color' => $event->color,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                    'url' => EventResource::getUrl(name: 'edit', parameters: ['record' => $event]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->label(__('Title')),
            ColorPicker::make('color')
                ->required()
                ->label(__('Color')),

            Grid::make()
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->required()
                        ->label(__('Start Date')),
                    DateTimePicker::make('ends_at')
                        ->required()
                        ->label(__('End Date')),
                ]),

            RichEditor::make('description')
                ->fileAttachmentsDirectory('events')
                ->columnSpanFull()
                ->label(__('Description')),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label(__("Add Event"))
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $form->fill([
                            'starts_at' => $arguments['start'] ?? null,
                            'ends_at' => $arguments['end'] ?? null
                        ]);
                    }
                )
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->label(__("View Event"));
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->label(__("Edit Event"))
                ->mountUsing(
                    function (Event $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->title,
                            'color' => $record->color,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at
                        ]);
                    }
                ),
            DeleteAction::make()
                ->label(__("Delete Event")),
        ];
    }
}
