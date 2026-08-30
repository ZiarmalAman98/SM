<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentGuardianResource\Pages;
use App\Models\ParentGuardian;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class ParentGuardianResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 3;

  public static function getNavigationGroup(): string
{
        return __('Student Management');
}

public static function getNavigationLabel(): string
{
    return __('Parent Accounts');
}

public static function getModelLabel(): string
{
    return __('Parent/Guardian');
}

public static function getPluralModelLabel(): string
{
    return __('Parents/Guardians');
}

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Hidden::make('type')
                        ->default('guardian')
                        ->dehydrated(),

                    Forms\Components\TextInput::make('name')
                        ->label(__("First Name"))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Enter first name')),

                    Forms\Components\TextInput::make('last_name')
                        ->label(__("Last Name"))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Enter last name')),

                    Forms\Components\Select::make('branch_id')
                        ->label(__('Branch Name'))
                        ->relationship('branch', 'branch_name')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->placeholder(__('Select branch')),

                    Forms\Components\TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->unique('users', 'email', ignoreRecord: true)
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Enter email')),

                    Forms\Components\TextInput::make('password')
                        ->label(__("Password"))
                        ->columnSpan(2)
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $context): bool => $context === 'create')
                        ->placeholder(__('Enter password')),

                    Fieldset::make(__('Parent/Guardian Information'))
                        ->relationship('parentGuardian')
                        ->schema([
                            Forms\Components\TextInput::make('family_code')
                                ->label(__('Family Code'))
                                ->default(fn() => ParentGuardian::generateFamilyCode())
                                ->required()
                                ->maxLength(50)
                                ->unique(
                                    table: 'parent_guardians',
                                    column: 'family_code',
                                    ignorable: fn(?ParentGuardian $record) => $record,
                                )
                                ->placeholder(__('Enter family code')),

                            Forms\Components\TextInput::make('address')
                                ->label(__("Address"))
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(255)
                                ->placeholder(__('Enter address')),
                        ]),

                    Fieldset::make(__('Contact Details'))
                        ->schema([
                            Repeater::make('contactDetails')
                                ->label(__('Contact Numbers'))
                                ->minItems(0)
                                ->relationship('contactDetails')
                                ->schema([
                                    TextInput::make('phone_number')
                                        ->label(__('Phone Number'))
                                        ->maxLength(20)
                                        ->required()
                                        ->placeholder(__('Enter phone number')),

                                    Select::make('phone_type')
                                        ->label(__('Phone Type'))
                                        ->options([
                                            'Mobile' => __('Mobile'),
                                            'Home' => __('Home'),
                                            'Office' => __('Office'),
                                            'Other' => __('Other'),
                                        ])
                                        ->native(false)
                                        ->required()
                                        ->placeholder(__('Select phone type')),

                                    Textarea::make('notes')
                                        ->label(__('Notes'))
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->nullable()
                                        ->placeholder(__('Enter notes')),

                                    Toggle::make('is_primary')
                                        ->label(__('Primary Contact'))
                                        ->columnSpanFull()
                                        ->default(false),
                                ])->columnSpanFull()->columns(2),
                        ])->columnSpanFull(),

                    Repeater::make('children1')
                        ->label(__('Linked Students'))
                        ->relationship('children1')
                        ->schema([
                            Select::make('student_id')
                                ->label(__('Student'))
                                ->options(fn() => \App\Models\User::where('type', 'student')->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->placeholder(__('Select student')),

                            Select::make('relationship')
                                ->label(__('Relationship'))
                                ->native(false)
                                ->options([
                                    'mother' => __('Mother'),
                                    'father' => __('Father'),
                                    'sibling' => __('Sibling'),
                                    'other' => __('Other'),
                                ])
                                ->required()
                                ->placeholder(__('Select relationship')),
                        ])
                        ->minItems(0)
                        ->defaultItems(0)
                        ->columnSpanFull()
                        ->columns(2),
                ])
                ->description(__("Parent/Guardian Form"))
                ->collapsed(false)
                ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'guardian'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name)
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('parentGuardian.family_code')
                    ->label(__('Family Code'))
                    ->default(__('Not Available'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('parentGuardian.address')
                    ->label(__('Address'))
                    ->default(__('Not Available'))
                    ->limit(50)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View')),
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentGuardians::route('/'),
            'create' => Pages\CreateParentGuardian::route('/create'),
            'edit' => Pages\EditParentGuardian::route('/{record}/edit'),
        ];
    }
}
