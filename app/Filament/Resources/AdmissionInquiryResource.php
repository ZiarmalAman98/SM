<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionInquiryResource\Pages;
use App\Filament\Resources\AdmissionInquiryResource\RelationManagers;
use App\Models\AdmissionInquiry;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdmissionInquiryResource extends Resource
{
    protected static ?string $model = AdmissionInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 1;
    public static function getNavigationGroup(): string
    {
        return __('Reception');
    }

    public static function getLabel(): string
    {
        return __('Admission Inquiry');
    }

    public static function getModelLabel(): string
    {
        return __('Admission Inquiry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Admission Inquiries');
    }

    public static function getNavigationLabel(): string
    {
        return __('Admission Inquiry');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Inquiry Details'))
                    ->schema([
                        Section::make()->schema([
                            Forms\Components\DateTimePicker::make('inquiry_date')
                                ->label(__('Inquiry Date'))
                                ->jalali()
                                ->default(Carbon::now()),

                            Forms\Components\Select::make('interested_class_id')
                                ->label(__('Interested Class'))
                                ->options(SchoolClass::all()->pluck('class_name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('inquiry_status')
                                ->label(__('Inquiry Status'))
                                ->options([
                                    'Pending' => __('Pending'),
                                    'Reviewed' => __('Reviewed'),
                                    'Accepted' => __('Accepted'),
                                    'Rejected' => __('Rejected'),
                                ])
                                ->native(false)
                                ->default('Pending')
                                ->required(),
                        ])->columns(3),

                        Forms\Components\Section::make(__('Parent Information'))
                            ->schema([
                                Forms\Components\TextInput::make('parent_name')
                                    ->label(__('Parent Name'))
                                    ->maxLength(100)
                                    ->required(),

                                Forms\Components\TextInput::make('phone_number')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->maxLength(20)
                                    ->required(),

                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->maxLength(100),

                                Forms\Components\Textarea::make('address')
                                    ->label(__('Address'))
                                    ->columnSpanFull()
                                    ->rows(3),
                            ])->columns(3),

                        Forms\Components\Section::make(__('Child Information'))
                            ->schema([
                                Forms\Components\TextInput::make('child_name')
                                    ->label(__('Child Name'))
                                    ->maxLength(100),

                                Forms\Components\DatePicker::make('child_dob')
                                    ->label(__('Child Date of Birth'))
                                    ->jalali()
                                    ->displayFormat('Y-m-d'),
                            ])->columns(2),

                        Forms\Components\Section::make(__('Additional Information'))
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label(__('Notes'))
                                    ->rows(4),
                            ]),
                    ])->description(__('Admission Inquiry Form'))->collapsed(false)->columns(2)
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent_name')
                    ->label(__('Parent'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('Phone'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('child_name')
                    ->label(__('Child'))
                    ->default(__('N/A'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('interestedClass.class_name')
                    ->label(__('Interested Class'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('inquiry_status')
                    ->label(__('Status'))
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->colors([
                        'warning' => __('Pending'),
                        'info' => __('Reviewed'),
                        'success' => __('Accepted'),
                        'danger' => __('Rejected'),
                    ]),

                Tables\Columns\TextColumn::make('inquiry_date')
                    ->label(__('Inquiry Date'))
                    ->jalaliDateTime()
                    // ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('interested_class_id')
                    ->label(__('Filter by Class'))
                    ->options(SchoolClass::all()->pluck('class_name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('inquiry_status')
                    ->label(__('Filter by Status'))
                    ->native(false)
                    ->options([
                        'Pending' => __('Pending'),
                        'Reviewed' => __('Reviewed'),
                        'Accepted' => __('Accepted'),
                        'Rejected' => __('Rejected'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),

                Tables\Actions\DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionInquiries::route('/'),
            'create' => Pages\CreateAdmissionInquiry::route('/create'),
            'edit' => Pages\EditAdmissionInquiry::route('/{record}/edit'),
        ];
    }
}
