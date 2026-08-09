<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?int $navigationSort = 4;


    public static function getLabel(): string
    {
        return __('Complaint');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Complaints');
    }

    public static function getNavigationLabel(): string
    {
        return __('Complaints');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reception');
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Hidden::make('complaint_date')
                    ->default(now())
                    ->dehydrated(),

                Select::make('complainant_type')
                    ->label(__('Complainant Type'))
                    ->native(false)
                    ->options([
                        'staff' => __('Staff'),
                        'student' => __('Student'),
                        'teacher' => __('Teacher'),
                        'guardian' => __('Guardian'),
                    ])
                    ->placeholder(__('Select complainant type'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('complainant_id', null))
                    ->required(),

                Select::make('complainant_id')
                    ->label(__('Complainant'))
                    ->native(false)
                    ->options(fn(Get $get) => self::getUsersByType($get('complainant_type')))
                    ->searchable()
                    ->placeholder(__('Select complainant'))
                    ->required(),

                TextInput::make('subject')
                    ->label(__('Subject'))
                    ->maxLength(150)
                    ->required()
                    ->placeholder(__('Enter complaint subject')),

                Select::make('status')
                    ->label(__('Status'))
                    ->native(false)
                    ->options([
                        'New' => __('New'),
                        'In Review' => __('In Review'),
                        'Resolved' => __('Resolved'),
                        'Closed' => __('Closed'),
                    ])
                    ->default('New')
                    ->placeholder(__('Select status'))
                    ->required(),

                RichEditor::make('description')
                    ->label(__('Description'))
                    ->fileAttachmentsDirectory('complaints')
                    ->columnSpanFull()
                    ->required()
                    ->placeholder(__('Enter complaint details')),

                RichEditor::make('resolution_notes')
                    ->label(__('Resolution Notes'))
                    ->columnSpanFull()
                    ->fileAttachmentsDirectory('complaints')
                    ->placeholder(__('Enter resolution notes')),
            ])
                ->description(__('Complaint Form'))
                ->collapsed(false)
                ->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_date')
                    ->label(__('Date'))
                    ->jalaliDateTime()
                    ->sortable()
                    ->tooltip(function ($record) {
                        // Ensure the date is parsed properly using Carbon
                        try {
                            return \Carbon\Carbon::parse($record->complaint_date)->format('F j, Y H:i');
                        } catch (\Exception $e) {
                            return $record->complaint_date; // Fallback to raw date string if parsing fails
                        }
                    }),

                TextColumn::make('complainant.name')
                    ->label(__('Complainant'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn($record) => $record->complainant->name ?? __('Not specified')),

                TextColumn::make('correspondent.name')
                    ->label(__('Correspondent'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->correspondent->name ?? __('Not specified')),

                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->subject),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->default(__('Not Available'))
                    ->html()
                    ->limit(50)
                    ->tooltip(fn($record) => strip_tags($record->description ?? __('No Notes')))
                    ->searchable(),

                SelectColumn::make('status')
                    ->label(__('Status'))
                    ->searchable()
                    ->options([
                        'New' => __('New'),
                        'In Review' => __('In Review'),
                        'Resolved' => __('Resolved'),
                        'Closed' => __('Closed'),
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('complainant_type')
                    ->label(__('Complainant Type'))
                    ->native(false)
                    ->options([
                        'staff' => __('Staff'),
                        'student' => __('Student'),
                        'teacher' => __('Teacher'),
                        'guardian' => __('Guardian'),
                    ])
                    ->placeholder(__('All Types')),

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->native(false)
                    ->options([
                        'New' => __('New'),
                        'In Review' => __('In Review'),
                        'Resolved' => __('Resolved'),
                        'Closed' => __('Closed'),
                    ])
                    ->placeholder(__('All Statuses')),

                SelectFilter::make('complainant_id')
                    ->label(__('Complainant'))
                    ->native(false)
                    ->options(fn() => \App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder(__('All Complainants')),
            ])
            ->defaultSort('complaint_date', 'desc')
            ->emptyStateHeading(__('No complaints found'))
            ->emptyStateDescription(__('Create your first complaint record'))
            ->emptyStateActions([
                \Filament\Tables\Actions\CreateAction::make()
                    ->label(__('Create Complaint')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected static function getUsersByType(?string $type): array
    {
        if (!$type) {
            return [];
        }

        return User::where('type', $type)
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}
