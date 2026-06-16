<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowBookResource\Pages;
use App\Filament\Resources\BorrowBookResource\RelationManagers\AddBookRelationManager;
use App\Filament\Resources\BorrowBookResource\RelationManagers\UserRelationManager;
use App\Models\BorrowBook;
use App\Models\AddBook;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BorrowBookResource extends Resource
{
    protected static ?string $model = BorrowBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 3;



    public static function getNavigationGroup(): string
    {
        return __('Library Management');
    }

    public static function getModelLabel(): string
    {
        return __('Borrow Book');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Borrowed Books');
    }

    public static function getNavigationLabel(): string
    {
        return __('Borrow Records');
    }
    // ==============================
    // 📋 FORM CONFIGURATION
    // ==============================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Borrow Information'))
                    ->schema([
                        Select::make('user_id')
                            ->label(__('Student'))
                            ->relationship(
                                'user',
                                'name',
                                fn($query) => $query->where('type', 'student')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('Select Student')),

                        Select::make('addBook_id')
                            ->label(__('Book'))
                            ->relationship(
                                'addBook',
                                'title',
                                fn($query) => $query->where('state', true)
                            )
                            ->preload()
                            ->searchable()
                            ->required()
                            ->placeholder(__('Select Book')),

                        DatePicker::make('borrow_date')
                            ->label(__('Borrow Date'))
                            ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                            ->default(Carbon::now())
                            ->jalali()
                            ->dehydrated(true)
                            ->placeholder(__('Select Borrow Date')),

                        DatePicker::make('return_date')
                            ->label(__('Return Date'))
                            ->nullable()
                            ->jalali()
                            ->placeholder(__('Select Return Date')),

                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull()
                            ->placeholder(__('Enter any additional notes')),
                    ])
                    ->columns(2)
            ]);
    }

    // ==============================
    // 📋 TABLE CONFIGURATION
    // ==============================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Borrower'))
                    ->limit(20)
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Deleted user'))
                    ->tooltip(fn($record) => $record->user?->name ?? __('Deleted user')),

                TextColumn::make('addBook.title')
                    ->label(__('Book Title'))
                    ->limit(20)
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Deleted book'))
                    ->tooltip(fn($record) => $record->addBook?->title ?? __('Deleted book')),

                TextColumn::make('borrow_date')
                    ->label(__('Borrow Date'))
                    ->sortable()
                    ->badge()
                    ->jalaliDate()
                    ->tooltip(fn($record) => $record->borrow_date?->format('F j, Y')),

                TextColumn::make('return_date')
                    ->label(__('Return Date'))
                    ->sortable()
                    ->badge()
                    ->jalaliDate()
                    ->tooltip(fn($record) => $record->return_date?->format('F j, Y') ?? __('Not returned'))
                    ->color(fn($state) => match (true) {
                        is_null($state) => 'gray',
                        now()->isBefore($state) => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->default(__('Not Available'))
                    ->html()
                    ->limit(30)
                    ->wrap()
                    ->tooltip(fn($record) => filled($record->description) ? strip_tags($record->description) : __('Not Available')),

                TextColumn::make('created_at')
                    ->label(__('Created On'))
                    ->sortable()
                    ->jalaliDate()
                    ->toggleable()
                    ->tooltip(fn($record) => $record->created_at?->format('F j, Y H:i')),
            ])
            ->filters([
                Tables\Filters\Filter::make('Overdue')
                    ->query(fn(Builder $query) => $query->whereDate('return_date', '<', now()))
                    ->label(__('Overdue Books')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View'))
                    ->tooltip(__('View')),

                Tables\Actions\EditAction::make()
                    ->label(__('Edit'))
                    ->tooltip(__('Edit')),

                Tables\Actions\DeleteAction::make()
                    ->label(__('Delelte'))
                    ->tooltip(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                ]),
            ])
            ->defaultSort('borrow_date', 'desc')
            ->emptyStateHeading(__('No records found'))
            ->emptyStateDescription(__('Create your first borrowing record'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Record')),
            ]);
    }

    // ==============================
    // 📋 RELATIONS CONFIGURATION
    // ==============================
    public static function getRelations(): array
    {
        return [
            AddBookRelationManager::class,
            UserRelationManager::class,
        ];
    }

    // ==============================
    // 📋 PAGE CONFIGURATION
    // ==============================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrowBooks::route('/'),
            'create' => Pages\CreateBorrowBook::route('/create'),
            'edit' => Pages\EditBorrowBook::route('/{record}/edit'),
        ];
    }
}
