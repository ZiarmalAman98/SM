<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\StudentVisitResource\Pages;
use App\Models\VisitorLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentVisitResource extends Resource
{
    protected static ?string $model = VisitorLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Reception');
    }

    public static function getLabel(): string
    {
        return __('My Visit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('My Visits');
    }

    public static function getNavigationLabel(): string
    {
        return __('My Visits');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('person_to_meet', auth()->user()->id)->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->where('person_to_meet', \Illuminate\Support\Facades\Auth::user()->id)
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')
                    ->label(__('Visitor Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('Phone Number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->default(__('N/A')),

                Tables\Columns\TextColumn::make('purpose')
                    ->label(__('Purpose'))
                    ->default(__('N/A'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('entry_time')
                    ->label(__('Entry Time'))
                    ->dateTime('F j, Y, g:i a')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('exit_time')
                    ->label(__('Exit Time'))
                    ->dateTime('F j, Y, g:i a')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Add any filters if necessary
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relationships if necessary
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentVisits::route('/'),
        ];
    }
}