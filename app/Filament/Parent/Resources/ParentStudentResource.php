<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\ParentStudentResource\Pages;
use App\Filament\Parent\Resources\ParentStudentResource\RelationManagers;
use App\Models\ParentStudent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParentStudentResource extends Resource
{
    protected static ?string $model = ParentStudent::class;

    protected static ?string $modelLabel = "My Children";

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Family');
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student Name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parentGuard.name')
                    ->label(__('Parent Guard Name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relationship'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Create At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('parent_guardian_id', auth()->id());
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
            'index' => Pages\ListParentStudents::route('/'),
        ];
    }
}
