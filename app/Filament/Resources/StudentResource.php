<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use Filament\Forms\Get;
use App\Models\User;
use App\Models\StudentCategory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Group;
use Morilog\Jalali\Jalalian;
use Filament\Forms\Components\Grid as FormsGrid;
use App\Models\FeeGroup;
use App\Models\FeeDiscount;


class StudentResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 2;
    public static function getNavigationGroup(): string
    {
        return __('Student Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Students');
    }

    public static function getModelLabel(): string
    {
        return __('Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Students');
    }

    public static function getLabel(): string
    {
        return __('Student');
    }
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Tabs::make()->tabs([
                    Tab::make(__("Student Info"))->schema([
                        ComponentsSection::make()
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Hidden::make('type')
                                            ->default('student')
                                            ->dehydrated(),
                                        TextInput::make('name')
                                            ->label(__("First Name"))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('last_name')
                                            ->label(__("Last Name"))
                                            // ->required()
                                            ->maxLength(255),
                                        TextInput::make('father_name')
                                            ->label(__("Father Name"))
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                TextInput::make('email')
                                    ->email()
                                    ->unique('users', 'email', ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('password')
                                    ->label(__("Password"))
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create'),

                                Fieldset::make(__('Student information'))->relationship('student')->schema([
                                    Card::make()->schema([
                                        TextInput::make('grand_father_name')
                                            ->label(__('Grand Father Name'))
                                            ->maxLength(255)
                                            ->nullable(),


                                        TextInput::make('tazkira_number')
                                            ->label(__('Tazkira Number'))
                                            ->maxLength(100)
                                            ->nullable(),
                                    ])->columns(2),
                                    Card::make()->schema([
                                        TextInput::make('admission_no')
                                            ->label(__('Admission No'))
                                            ->unique('students', 'admission_no', ignoreRecord: true)
                                            ->required()
                                            ->maxLength(50),
                                        TextInput::make('roll_no')
                                            ->label(__('Roll No'))
                                            ->unique('students', 'roll_no', ignoreRecord: true)
                                            ->required()
                                            ->maxLength(20),
                                        Select::make('gender')
                                            ->label(__('Gender'))
                                            ->options([
                                                'Male' => __('Male'),
                                                'Female' => __('Female'),
                                                'Other' => __('Other')
                                            ])
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('category_id')
                                            ->label(__('Category'))
                                            ->options(StudentCategory::pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('section_id')
                                            ->label(__('Section'))
                                            ->options(\App\Models\Section::pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ])->columns(5),

                                    Card::make()->schema([
                                        DatePicker::make('dob')
                                            ->label(__('Date of Birth'))
                                            ->jalali()  // Enable Jalali calendar
                                            ->displayFormat('Y/m/d')  // Display format (e.g., 1402/05/15)
                                            ->locale('fa')  // Persian/Farsi locale
                                            // ->required()
                                            ->maxDate(now())  // Prevent future dates for date of birth
                                            ->placeholder(__('Select date of birth')),

                                        DatePicker::make('admission_date')
                                            ->label(__('Admission Date'))
                                            ->jalali()
                                            ->displayFormat('Y/m/d')
                                            ->locale('fa')
                                            ->default(now())  // Default to current date
                                            ->nullable()
                                            ->placeholder(__('Select admission date')),
                                        Select::make('blood_group')->label(__('Blood Group'))->native(false)->options([
                                            'A+' => 'A+',
                                            'A-' => 'A-',
                                            'B+' => 'B+',
                                            'B-' => 'B-',
                                            'O+' => 'O+',
                                            'O-' => 'O-',
                                            'AB+' => 'AB+',
                                            'AB-' => 'AB-'
                                        ])->nullable(),
                                        TextInput::make('caste')->label(__('Caste'))->maxLength(50)->nullable(),
                                        TextInput::make('height')->label(__('Height (cm)'))->numeric()->step(0.1)->nullable(),
                                        TextInput::make('weight')->label(__('Weight (kg)'))->numeric()->step(0.1)->nullable(),
                                    ])->columns(3),

                                    Card::make()->schema([
                                        FormsGrid::make(2)->schema([
                                            TextInput::make('address')->label(__('Address'))->maxLength(255)->nullable(),
                                            TextInput::make('phone')->label(__('Phone Number'))->maxLength(255)->nullable(),
                                        ]),
                                        FileUpload::make('photo_path')
                                            ->label(__('Student Photo'))
                                            ->disk('public')
                                            ->directory('student_photos')
                                            ->visibility('public')
                                            ->image()
                                            ->nullable(),
                                    ]),
                                ]),

                                Repeater::make('children')
                                    ->addActionLabel(__('Add Another'))
                                    ->label(__('Linked Guardian'))
                                    ->relationship('children')
                                    ->schema([
                                        Select::make('parent_guardian_id')
                                            ->label(__('Guardian Or Siblings'))
                                            ->default('21')

                                            ->options(
                                                fn() => User::whereIn('type', ['guardian', 'student'])
                                                    ->get()
                                                    ->mapWithKeys(fn($user) => [
                                                        $user->id => "{$user->name} ({$user->email})"
                                                    ])
                                            )
                                            ->searchable()
                                            ->required(),
                                        Select::make('relationship')
                                            ->label(__('Relationship'))
                                            ->native(false)
                                            ->options([
                                                'mother' => __('Mother'),
                                                'father' => __('Father'),
                                                'sibling' => __('Sibling'),
                                                'other' => __('Other')
                                            ])
                                            ->required(),
                                    ])->columnSpanFull()->columns(2),
                            ])->columns(2)
                    ]),
                    Tab::make(__("Class"))->schema([
                        Repeater::make("studentClasses")
                            ->relationship("studentClasses")
                            ->minItems(0)
                            ->defaultItems(0)
                            ->schema([
                                Select::make('class_id')
                                    ->label(__('Class'))
                                    ->relationship('schoolClass', 'class_name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('academic_year')
                                    ->label(__('Academic Year'))
                                    ->options(array_combine(range(2010, 2099), range(2010, 2099)))
                                    ->searchable()
                                    ->required(),
                                Select::make('status')
                                    ->label(__('Status'))
                                    ->native(false)
                                    ->options([
                                        'active' => __('Active'),
                                        'completed' => __('Completed'),
                                        'transferred' => __('Transferred')
                                    ])
                                    ->default('active')
                                    ->required(),
                            
                            ])->columns(3)
                    ]),
                    Tab::make(__('Fees Assignment'))->schema([
                        Group::make()
                            ->relationship('student')
                            ->schema([
                                Repeater::make('feeGroupAssignments')
                                    ->relationship()
                                    ->label(__('Fee Assignments'))
                                    ->addActionLabel(__('Add Fee Assignment'))
                                    ->minItems(0)
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->schema([
                                        Select::make('fee_group_id')
                                            ->label(__('Fee Group'))
                                            ->options(fn () => FeeGroup::pluck('group_name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->required()
                                            ->placeholder(__('Select fee group')),

                                        DatePicker::make('effective_date')
                                            ->label(__('Effective Date'))
                                            ->jalali()
                                            ->default(now())
                                            ->placeholder(__('Select effective date')),

                                        Select::make('fee_discount_id')
                                            ->label(__('Fee Discount'))
                                            ->options(fn () => FeeDiscount::pluck('discount_name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->nullable()
                                            ->placeholder(__('Select fee discount (optional)')),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'student'))
            ->columns([
                ImageColumn::make('student.photo_path')
                    ->label(__('Photo'))
                    ->circular()
                    ->defaultImageUrl(url('https://cdn-icons-png.flaticon.com/512/2940/2940652.png')),

                TextColumn::make('student.admission_no')
                    ->label(__('Admission No'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.roll_no')
                    ->label(__('Roll No'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('Student Name'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name)
                    ->searchable(),


                TextColumn::make('father_name')
                    ->label(__('Student Father Name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.category.name')
                    ->label(__('Category')),

                TextColumn::make('student.dob')
                    ->label(__('Date of Birth'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable(),

                TextColumn::make('student.admission_date')
                    ->label(__('Admission Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable(),

                TextColumn::make('student.blood_group')
                    ->label(__('Blood Group'))
                    ->badge()
                    ->sortable()
                    ->default(__('N/A'))
                    ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('student.category_id')
                    ->label(__('Filter by Category'))
                    ->relationship('student.category', 'name')
                    ->options(StudentCategory::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('student.section_id')
                    ->label(__('Filter by Section'))
                    ->relationship('student.section', 'name')
                    ->options(\App\Models\Section::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                Action::make('ID Card')
                    ->label(__('ID Card'))
                    ->icon('heroicon-o-identification')
                    ->url(fn(User $record) => route('students.id-card', ['student' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('Fee Card')                     //  ✅ NEW
                    ->label(__('Fee Card'))
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->url(fn(User $record) => route('students.fee-card', ['student' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('User Details'))->schema([
                    TextEntry::make('name')
                        ->label(__('Full Name')),

                    TextEntry::make('email')
                        ->label(__('Email')),
                ])->columns(2),

                Section::make(__('Student Information'))->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('student.admission_no')
                            ->label(__('Admission No')),

                        TextEntry::make('student.roll_no')
                            ->label(__('Roll No')),

                        TextEntry::make('student.category.name')
                            ->label(__('Category')),
                    ]),
                ]),

                Section::make(__('Personal Information'))->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('student.dob')
                            ->label(__('Date of Birth')),

                        TextEntry::make('student.admission_date')
                            ->label(__('Admission Date')),

                        TextEntry::make('student.blood_group')
                            ->label(__('Blood Group')),

                        TextEntry::make('student.caste')
                            ->label(__('Caste'))
                            ->default(__('Not Specified')),
                    ]),
                ]),

                Section::make(__('Physical Details'))->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('student.height')
                            ->label(__('Height (cm)')),

                        TextEntry::make('student.weight')
                            ->label(__('Weight (kg)')),
                    ]),
                ]),

                Section::make(__('Address & Photo'))->schema([
                    TextEntry::make('student.address')
                        ->label(__('Address'))
                        ->default(__('No Address Provided')),

                    ImageEntry::make('student.photo_path')
                        ->label(__('Student Photo'))
                        ->defaultImageUrl(url('https://cdn-icons-png.flaticon.com/512/2940/2940652.png')),
                ]),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
