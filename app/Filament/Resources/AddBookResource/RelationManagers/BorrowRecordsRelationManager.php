<?php

namespace App\Filament\Resources\AddBookResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'borrowRecords';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('Student'))
                    ->relationship(
                        'user',  // Relationship name
                        'name',  // Column to display
                        fn($query) => $query->where('type', 'student') // ✅ Filter by `student` type
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                // Select::make('addBook_id')
                //     ->label('Book')
                //     ->relationship(
                //         'addBook',
                //         'title',
                //         fn($query) => $query->where('state', true)
                //     )
                //     ->preload()
                //     ->searchable()
                //     ->required(),

                DatePicker::make('borrow_date')
                    ->label(__('Borrow Date'))
                    ->disabled()
                    ->default(Carbon::now())
                    ->dehydrated(true),
                DatePicker::make('return_date')
                    ->label(_('Return Date'))
                    ->nullable()
                    ->columnSpanFull(),


                RichEditor::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->placeholder(__('Enter assignment details')),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Borrower'))
                    ->limit(20)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('addBook.title')
                    ->label(__('Book Title'))
                    ->limit(20)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('borrow_date')
                    ->label(_('Borrow Date'))
                    ->sortable()
                    ->badge()
                    ->date(),

                TextColumn::make('return_date')
                    ->label(__('Return Date'))
                    ->sortable()
                    ->badge()
                    ->date()
                    ->color(fn($state) => match (true) {
                        is_null($state) => 'gray',
                        now()->isBefore($state) => 'success',
                        default => 'gray',
                    }),


                TextColumn::make('description')
                    ->label(__('Description'))
                    ->default(__('Not Available '))
                    ->html() // ✅ Required for rendering HTML content
                    ->limit(30)
                    ->wrap(),


                TextColumn::make('created_at')
                    ->label(__('Created On'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('Y-m-d')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
