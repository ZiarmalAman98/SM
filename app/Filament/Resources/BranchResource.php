<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\User;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\CreateAction;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Branches');
    }

    public static function getModelLabel(): string
    {
        return __('Branch');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Branches');
    }

    public static function getLabel(): string
    {
        return __('Branch');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('branch_name')
                    ->required()
                    ->label(__('Branch Name'))
                    ->maxLength(255)
                    ->placeholder(__('Enter branch name')),

                Select::make('branch_manager_name')
                    ->label(__('Branch Manager'))
                    ->options(fn () => User::query()
                        ->where('type', 'staff')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder(__('Select branch manager')),

                RichEditor::make('branch_description')
                    ->fileAttachmentsDirectory('branch_descriptions')
                    ->fileAttachmentsVisibility('visible')
                    ->label(__('Branch Description'))
                    ->placeholder(__('Enter a detailed description of the branch'))
                    ->columnSpanFull(),

                TextInput::make('branch_email')
                    ->email()
                    ->label(__('Email'))
                    ->maxLength(255)
                    ->placeholder(__('Enter branch email')),

                TextInput::make('branch_phone')
                    ->label(__('Phone'))
                    ->maxLength(20)
                    ->placeholder(__('Enter branch phone number')),

                TextInput::make('branch_website')
                    ->label(__('Website'))
                    ->url()
                    ->maxLength(255)
                    ->placeholder(__('Enter branch website URL')),

                TextInput::make('monthly_budget')
                    ->label(__('Monthly Budget'))
                    ->numeric()
                    ->prefix('AFN')
                    ->default(0.00)
                    ->placeholder(__('Enter monthly budget amount')),

                Textarea::make('branch_address')
                    ->label(__('Address'))
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder(__('Enter full branch address')),

                Toggle::make('branch_status')
                    ->label(__('Branch Status'))
                    ->required()
                    ->default('active')
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch_name')
                    ->label(__('Branch Name'))
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn($record) => $record->branch_name),

                TextColumn::make('branch_phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->tooltip(fn($record) => $record->branch_phone),

                TextColumn::make('branch_email')
                    ->label(__('Email'))
                    ->searchable()
                    ->tooltip(fn($record) => $record->branch_email),

                TextColumn::make('monthly_budget')
                    ->label(__('Monthly Budget'))
                    ->money('AFN', true)
                    ->tooltip(fn($record) => number_format($record->monthly_budget, 2) . ' AFN'),

                BooleanColumn::make('branch_status')
                    ->label(__('Status'))
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('branch_status')
                    ->label(__('Status'))
                    ->options([
                        'active' => __('Active'),
                        'inactive' => __('Inactive'),
                        'closed' => __('Closed'),
                    ])
                    ->placeholder(__('All Statuses')),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // Define actions if needed
            ])
            ->emptyStateHeading(__('No branches found'))
            ->emptyStateDescription(__('Create your first branch record'))
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('Create Branch')),
            ]);
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
