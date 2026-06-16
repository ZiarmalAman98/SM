<?php

namespace App\Filament\Resources\AssignmentResource\RelationManagers;

use App\Filament\Resources\TeacherResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherRelationManager extends RelationManager
{
    protected static string $relationship = 'Teacher';

    public function form(Form $form): Form
    {
        return $form->schema(TeacherResource::getFormSchema()); // ✅ Correct usage
    }

    public function table(Table $table): Table
    {
        return TeacherResource::table($table);

    }
}
