<?php

namespace App\Filament\Resources\BorrowBookResource\RelationManagers;

use App\Filament\Resources\AddBookResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AddBookRelationManager extends RelationManager
{
    protected static string $relationship = 'addBook';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Book Information')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Book Title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter book title'),

                    Forms\Components\TextInput::make('author')
                        ->label('Author')
                        ->required()
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Enter author name'),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->columnSpanFull()
                        ->placeholder(__('Enter assignment details')),

                    Forms\Components\FileUpload::make('file')
                        ->label('Upload Book File')
                        ->required()
                        ->disk('public')
                        ->directory('books')
                        ->openable()
                        ->deletable()
                        ->maxSize(2048) // 2 MB limit
                        ->columnSpanFull()
                        ->acceptedFileTypes(['application/pdf'])
                        ->downloadable(),

                    Forms\Components\Toggle::make('state')
                        ->label('Book State')
                        ->onIcon('heroicon-o-check-circle')
                        ->offIcon('heroicon-o-x-circle')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true),

                    Forms\Components\Hidden::make('user_id')
                        ->default(auth()->id()),  // Auto-assign logged-in user
                ])
                ->columns(2)
        ]);
}


public function table(Table $table): Table
{
    return AddBookResource::table($table);

}

}
