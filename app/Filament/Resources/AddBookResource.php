<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddBookResource\Pages;
use App\Filament\Resources\AddBookResource\RelationManagers\BorrowRecordsRelationManager;
use App\Models\AddBook;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class AddBookResource extends Resource
{
    protected static ?string $model = AddBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;

    //    public static function getNavigationGroup(): string
    // {
    //     return __('Library Management');
    // }

    // public static function getModelLabel(): string
    // {
    //     return __('Book');
    // }

    // public static function getPluralModelLabel(): string
    // {
    //     return __('Books');
    // }

    // public static function getNavigationLabel(): string
    // {
    //     return __('Books');
    // }
    public static function getNavigationGroup(): string
    {
        return __('Library Management');
    }

    public static function getLabel(): string
    {
        return __('Book');
    }

    public static function getModelLabel(): string
    {
        return __('Book');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Books');
    }

    public static function getNavigationLabel(): string
    {
        return __('Books');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('state', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('user_id', auth()->id());
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Section::make(__('Book Information'))
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label(__('Book Title'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('Enter book title')),

                            Forms\Components\TextInput::make('author')
                                ->label(__('Author'))
                                ->required()
                                ->nullable()
                                ->maxLength(255)
                                ->placeholder(__('Enter author name')),

                            RichEditor::make('description')
                                ->label(__('Description'))
                                ->columnSpanFull()
                                ->placeholder(__('Enter book details')),

                            Forms\Components\FileUpload::make('file')
                                ->label(__('Upload Book File'))
                                ->disk('public')
                                ->directory('books')
                                ->openable()
                                ->deletable()
                                ->maxSize(2048)
                                ->columnSpanFull()
                                ->acceptedFileTypes(['application/pdf'])
                                ->downloadable(),

                            Forms\Components\Toggle::make('state')
                                ->label(__('Book State'))
                                ->onIcon('heroicon-o-check-circle')
                                ->offIcon('heroicon-o-x-circle')
                                ->onColor('success')
                                ->offColor('danger')
                                ->default(true),

                            Forms\Components\Hidden::make('user_id')
                                ->default(auth()->id()),
                        ])
                        ->columns(2)
                ])->description(__("Add Book Form"))->collapsed(false)->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->limit(15)
                    ->tooltip(fn($state) => strlen($state) > 30 ? $state : null),

                Tables\Columns\TextColumn::make('author')
                    ->label(__('Author'))
                    ->sortable()
                    ->limit(15)
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(20)
                    ->html()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Added On'))
                    ->sortable()
                    ->jalaliDate(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Added By'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('state')
                    ->label(__('Book State'))
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),

                Tables\Columns\IconColumn::make('file')
                    ->label(__('PDF File'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn($record) => Storage::url($record->file))
                    ->openUrlInNewTab()
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('state')
                    ->label(__('Book State'))
                    ->trueLabel(__('New'))
                    ->falseLabel(__('Used/Damaged'))
                    ->placeholder(__('All States'))
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('Edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Delete')),
                    Tables\Actions\ViewAction::make()
                        ->label(__('View')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            BorrowRecordsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddBooks::route('/'),
            'create' => Pages\CreateAddBook::route('/create'),
            'edit' => Pages\EditAddBook::route('/{record}/edit'),
            'view' => Pages\ViewAddBook::route('/{record}'),
        ];
    }
}
