<?php

namespace App\Filament\Parent\Resources;

use App\Models\DisciplineForm;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Parent\Resources\DisciplineResource\Pages;
use Morilog\Jalali\Jalalian;

class DisciplineResource extends Resource
{
    protected static ?string $model = DisciplineForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label(__('Student Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('staff.user.name')
                    ->label(__('Staff Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('Reason'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status')),

                Tables\Columns\BooleanColumn::make('family_notified')
                    ->label(__('Family Notified')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime('Y/m/d H:i') // or 'd F Y'
                    ->sortable(),

            ])
            ->filters([])
            ->actions([
                // Optional actions here
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Add bulk actions if needed
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('family_notified', true)
            ->whereHas('student', function ($studentQuery) {
                $studentQuery->whereHas('user', function ($userQuery) {
                    $userQuery->whereHas('parent', function ($parentQuery) {
                        $parentQuery->where('parent_guardian_id', Auth::id());
                    });
                });
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisciplines::route('/'),
            'create' => Pages\CreateDiscipline::route('/create'),
        ];
    }
}
