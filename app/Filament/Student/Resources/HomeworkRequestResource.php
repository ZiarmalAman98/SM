<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\HomeworkRequestResource\Pages;
use App\Filament\Student\Resources\HomeworkRequestResource\RelationManagers;
use App\Models\Assignment;
use Illuminate\Support\Str;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HomeworkRequestResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Homeworks Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Homeworks Requests');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('subject.schoolClass.studentClasses', fn(Builder $query) => $query->where('student_id', auth()->user()->id))
            ->where('deadline', '>=', now())
            ->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->whereHas('subject.schoolClass.studentClasses', fn(Builder $query) => $query->where('student_id', auth()->user()->id)))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject Class'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deadline')
                    ->label(__('Due Date'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Request At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filters
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infoList(Infolist $infoList): Infolist
    {
        return $infoList
            ->schema([
                Section::make(__('Homework Details'))
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('Homework Title'))
                            ->tooltip(__('Click to download the attachment')),
                        TextEntry::make('subject.name')
                            ->label(__('Class Name')),
                        TextEntry::make('subject.name')
                            ->label(__('Subject')),
                        TextEntry::make('subject.teacher.name')
                            ->label(__('Teacher')),
                    ])
                    ->columns(2),

                Section::make(__('Dates'))
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Assigned At')),
                        TextEntry::make('deadline')
                            ->label(__('Due Date')),
                    ])
                    ->columns(2),

                Section::make(__('Description'))
                    ->schema([
                        TextEntry::make('description')
                            ->html()
                            ->label(__('Description'))
                            ->placeholder(__('No description provided')),
                    ])
                    ->columnSpanFull(),

                Section::make(__('Files'))
                    ->schema([
                        ImageEntry::make('file_path')
                            ->label(__('Attachment'))
                            ->state(function ($record) {
                                $filePath = $record->file_path;
                                $extension = Str::lower(pathinfo($filePath, PATHINFO_EXTENSION));
                                
                                return in_array($extension, ['jpg', 'jpeg', 'png'])
                                    ? $filePath
                                    : asset('images/file.png');
                            })
                            ->url(fn($record) => asset('storage/' . $record->file_path), true),
                    ])
                    ->columnSpanFull()
            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            // Relations
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeworkRequests::route('/'),
            'view' => Pages\ViewHomeworkRequest::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Homeworks');
    }
}