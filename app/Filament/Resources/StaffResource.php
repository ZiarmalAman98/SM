<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Morilog\Jalali\Jalalian;


class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return __('Staff Account');
    }

    public static function getModelLabel(): string
    {
        return __('Staff Account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Staff Accounts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Staff Accounts');
    }

    public static function getNavigationGroup(): string
    {
        return __('Account Management');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make(__('Staff Information'))->schema([
                    Hidden::make('type')
                        ->default('staff')
                        ->dehydrated(),

                    Forms\Components\TextInput::make('name')
                        ->label(__("First Name"))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('last_name')
                        ->label(__("Last Name"))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('branch_id')
                        ->label(__('Branch Name'))
                        ->relationship('branch', 'branch_name')
                        ->searchable()
                        ->required()
                        ->preload(),

                    TextInput::make('email')
                        ->email()
                        ->unique('users', 'email', ignoreRecord: true)
                        ->required()
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label(__("Password"))
                        ->password()
                        ->revealable()
                        ->columnSpan(2)
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $context): bool => $context === 'create'),

                    // All Staff Data Grouped Under staff Relationship
                    Group::make()
                        ->relationship('staff')
                        ->schema([
                            Tabs::make(__('Staff Details'))
                                ->tabs([
                                    Tabs\Tab::make(__('Basic Information'))
                                        ->schema([
                                            Fieldset::make(__('Personal Info'))
                                                ->schema([
                                                    Select::make('title')
                                                        ->label(__('Title'))
                                                        ->options([
                                                            'Mr' => __('Mr'),
                                                            'Mrs' => __('Mrs'),
                                                            'Ms' => __('Ms'),
                                                            'Dr' => __('Dr'),
                                                            'Prof' => __('Prof'),
                                                            'Sir' => __('Sir'),
                                                            'Madam' => __('Madam'),
                                                            'Lady' => __('Lady'),
                                                        ])
                                                        ->nullable()
                                                        ->native(false),

                                                    Select::make('designation')
                                                        ->label(__('Designation'))
                                                        ->options([
                                                            'Principal' => __('Principal'),
                                                            'Vice Principal' => __('Vice Principal'),
                                                            'Administrator' => __('Administrator'),
                                                            'Accountant' => __('Accountant'),
                                                            'Librarian' => __('Librarian'),
                                                            'Clerk' => __('Clerk'),
                                                            'IT Support' => __('IT Support'),
                                                            'Teacher' => __('Teacher'),
                                                            'Assistant Teacher' => __('Assistant Teacher'),
                                                            'Subject Coordinator' => __('Subject Coordinator'),
                                                            'Head of Department' => __('Head of Department'),
                                                            'Sports Coordinator' => __('Sports Coordinator'),
                                                            'Counselor' => __('Counselor'),
                                                            'Nurse' => __('Nurse'),
                                                            'Security' => __('Security'),
                                                            'Janitor' => __('Janitor'),
                                                            'Receptionist' => __('Receptionist'),
                                                            'Front Desk Officer' => __('Front Desk Officer'),
                                                            'Admissions Officer' => __('Admissions Officer'),
                                                            'HR Manager' => __('HR Manager'),
                                                            'Payroll Officer' => __('Payroll Officer'),
                                                            'Event Coordinator' => __('Event Coordinator'),
                                                            'Teaching Assistant' => __('Teaching Assistant'),
                                                            'Lab Assistant' => __('Lab Assistant'),
                                                            'School Driver' => __('School Driver'),
                                                            'Maintenance Staff' => __('Maintenance Staff'),
                                                            'Cleaner' => __('Cleaner'),
                                                            'Bus Conductor' => __('Bus Conductor'),
                                                        ])
                                                        ->native(false)
                                                        ->searchable()
                                                        ->required(),

                                                    Select::make('department')
                                                        ->label(__('Department'))
                                                        ->options([
                                                            'Science' => __('Science'),
                                                            'Mathematics' => __('Mathematics'),
                                                            'English' => __('English'),
                                                            'History' => __('History'),
                                                            'Geography' => __('Geography'),
                                                            'Art' => __('Art'),
                                                            'Physical Education' => __('Physical Education'),
                                                            'Music' => __('Music'),
                                                            'Languages' => __('Languages'),
                                                            'Social Studies' => __('Social Studies'),
                                                            'Computer Science' => __('Computer Science'),
                                                            'IT Support' => __('IT Support'),
                                                            'Administration' => __('Administration'),
                                                            'Library' => __('Library'),
                                                            'Sports' => __('Sports'),
                                                            'Counseling' => __('Counseling'),
                                                            'Special Education' => __('Special Education'),
                                                            'Maintenance' => __('Maintenance'),
                                                            'Human Resources' => __('Human Resources'),
                                                            'Finance' => __('Finance'),
                                                            'Security' => __('Security'),
                                                        ])
                                                        ->nullable()
                                                        ->native(false)
                                                        ->searchable(),

                                                    TextInput::make('father_name')->label(__("Father's Name"))->nullable(),
                                                    TextInput::make('mother_name')->label(__("Mother's Name"))->nullable(),

                                                    Select::make('gender')->options([
                                                        'Male' => __('Male'),
                                                        'Female' => __('Female'),
                                                        'Other' => __('Other'),
                                                    ])->native(false)->required(),

                                                    Select::make('marital_status')->options([
                                                        'Single' => __('Single'),
                                                        'Married' => __('Married'),
                                                        'Divorced' => __('Divorced'),
                                                        'Widowed' => __('Widowed'),
                                                    ])->native(false)->nullable(),

                                                    DatePicker::make('date_of_birth')
                                                        ->label(__('Date of Birth'))
                                                        ->jalali()
                                                        ->displayFormat('Y/m/d')
                                                        ->locale('fa')
                                                        ->nullable(),

                                                    DatePicker::make('joining_date')
                                                        ->label(__('Joining Date'))
                                                        ->jalali()
                                                        ->displayFormat('Y/m/d')
                                                        ->locale('fa')
                                                        ->default(now()),

                                                    FileUpload::make('photo')->label(__('Photo'))
                                                        ->image()
                                                        ->imageEditor()
                                                        ->directory('staff/photos')
                                                        ->columnSpanFull()
                                                        ->nullable(),
                                                ])->columns(3),
                                        ]),

                                    Tabs\Tab::make(__('Address & Contact'))
                                        ->schema([
                                            Textarea::make('current_address')->label(__('Current Address'))->rows(3)->columnSpanFull(),
                                            Textarea::make('permanent_address')->label(__('Permanent Address'))->rows(3)->columnSpanFull(),
                                        ]),

                                    Tabs\Tab::make(__('Job & Salary'))
                                        ->schema([
                                            Select::make("qualification")
                                                ->label(__("Qualification"))
                                                ->options([
                                                    "None" => __("None"),
                                                    "High School" => __("High School"),
                                                    "Associate Degree" => __("Associate Degree"),
                                                    "Bachelor's Degree" => __("Bachelor's Degree"),
                                                    "Master's Degree" => __("Master's Degree"),
                                                    "Doctorate" => __("Doctorate"),
                                                    "PhD" => __("PhD"),
                                                    "Diploma" => __("Diploma"),
                                                    "Certificate" => __("Certificate"),
                                                    "PG Diploma" => __("Postgraduate Diploma"),
                                                    "MBA" => __("MBA"),
                                                    "MSc" => __("MSc"),
                                                    "BSc" => __("BSc"),
                                                    "BE" => __("B.E. (Bachelor of Engineering)"),
                                                    "ME" => __("M.E. (Master of Engineering)"),
                                                    "BTech" => __("B.Tech (Bachelor of Technology)"),
                                                    "MTech" => __("M.Tech (Master of Technology)"),
                                                    "MPhil" => __("MPhil"),
                                                    "LLB" => __("LLB"),
                                                    "LLM" => __("LLM"),
                                                    "Other" => __("Other"),
                                                ])
                                                ->nullable()
                                                ->native(false),
                                            TextInput::make('work_experience')->label(__('Work Experience (years)'))->numeric()->nullable(),
                                            TextInput::make('basic_salary')->label(__('Basic Salary'))->numeric()->required()->prefix('AFN')->step(0.01),
                                            Select::make('contract_type')->options([
                                                'Permanent' => __('Permanent'),
                                                'Temporary' => __('Temporary'),
                                                'Intern' => __('Intern'),
                                                'Freelance' => __('Freelance'),
                                                'Part-Time' => __('Part-Time'),
                                                'Full-Time' => __('Full-Time'),
                                                'Contractor' => __('Contractor'),
                                                'Apprenticeship' => __('Apprenticeship'),
                                                'Seasonal' => __('Seasonal'),
                                                'Volunteer' => __('Volunteer'),
                                            ])->native(false)->nullable(),

                                            TimePicker::make('work_from')->label(__('Work From'))->nullable(),
                                            TimePicker::make('work_to')->label(__('Work To'))->nullable(),
                                            Textarea::make('note')->label(__('Note'))->rows(3)->nullable()->columnSpanFull(),
                                        ])->columns(2),

                                    Tabs\Tab::make(__('Bank Info'))
                                        ->schema([
                                            TextInput::make('bank_account_number')->label(__('Account Number'))->maxLength(50),
                                            TextInput::make('bank_name')->label(__('Bank Name'))->maxLength(100),
                                            TextInput::make('ifsc_code')->label(__('IFSC Code'))->maxLength(50),
                                            TextInput::make('bank_branch')->label(__('Bank Branch'))->maxLength(100),
                                        ])->columns(2),
                                ])
                                ->columnSpanFull(),
                        ])->columnSpanFull(),

                    // Repeater: Contact Details
                    Fieldset::make(__('Staff Contact Details'))
                        ->schema([
                            Repeater::make('contactDetails')
                                ->relationship('contactDetails')
                                ->label(__('Phone Numbers'))
                                ->schema([
                                    TextInput::make('phone_number')->label(__('Phone Number'))->maxLength(20)->required(),
                                    Select::make('phone_type')->label(__('Phone Type'))->options([
                                        'Mobile' => __('Mobile'),
                                        'Home' => __('Home'),
                                        'Office' => __('Office'),
                                        'Emergency' => __('Emergency'),
                                        'Other' => __('Other'),
                                    ])->native(false)->required(),
                                    Textarea::make('notes')->label(__('Notes'))->rows(3)->nullable()->columnSpanFull(),
                                    Toggle::make('is_primary')->label(__('Primary Contact'))->default(false)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->addActionLabel(__('Add Phone')),
                        ])
                        ->columnSpanFull(),

                    // Repeater: Social Media Links
                    Fieldset::make(__('Social Media Details'))
                        ->schema([
                            Repeater::make('socialMediaLinks')
                                ->relationship('socialMediaLinks')
                                ->label(__('Social Media Links'))
                                ->schema([
                                    Select::make('name')
                                        ->label(__('Platform'))
                                        ->options([
                                            'Facebook' => __('Facebook'),
                                            'Instagram' => __('Instagram'),
                                            'Twitter' => __('Twitter'),
                                            'LinkedIn' => __('LinkedIn'),
                                            'TikTok' => __('TikTok'),
                                            'Snapchat' => __('Snapchat'),
                                            'Pinterest' => __('Pinterest'),
                                            'YouTube' => __('YouTube'),
                                            'Reddit' => __('Reddit'),
                                            'WhatsApp' => __('WhatsApp'),
                                            'Telegram' => __('Telegram'),
                                            'Discord' => __('Discord'),
                                            'Skype' => __('Skype'),
                                            'Other' => __('Other'),
                                        ])
                                        ->native(false)
                                        ->searchable()
                                        ->nullable(),

                                    TextInput::make('url')
                                        ->label(__('Profile URL'))
                                        ->url(),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->defaultItems(0)
                                ->addActionLabel(__('Add Link')),
                        ])
                        ->columnSpanFull(),

                    // Repeater: User Documents
                    Fieldset::make(__('Staff Documents'))
                        ->schema([
                            Repeater::make('userDocuments')
                                ->relationship('userDocuments')
                                ->label(__('Documents'))
                                ->schema([
                                    Select::make("name")
                                        ->label(__("Document Type"))
                                        ->columnSpanFull()
                                        ->options([
                                            "Image" => __("Image"),
                                            "Resume" => __("Resume"),
                                            "Work Experience" => __("Work Experience"),
                                            "Certificate" => __("Certificate"),
                                            "Diploma" => __("Diploma"),
                                            "ID Proof" => __("ID Proof"),
                                            "Address Proof" => __("Address Proof"),
                                            "Reference Letter" => __("Reference Letter"),
                                            "Other" => __("Other"),
                                        ])
                                        ->native(false)
                                        ->searchable(),

                                    FileUpload::make('file')
                                        ->label(__('Upload File'))
                                        ->directory('user-documents')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->defaultItems(0)
                                ->addActionLabel(__('Add Document')),
                        ])
                        ->columnSpanFull(),
                    Forms\Components\CheckboxList::make('roles')
                        ->relationship('roles', 'name')
                        ->searchable(),
                ])->description(__("Staff Form"))->collapsed(false)->columns(3)
            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'staff'))
            ->columns([
                Tables\Columns\ImageColumn::make('staff.photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->default(asset('logo.png'))
                    ->height(40)
                    ->width(40),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Staff Name'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('staff.designation')
                    ->label(__('Designation'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('staff.basic_salary')
                    ->label(__('Salary'))
                    ->sortable()
                    ->searchable()
                    ->default(__('N/A'))
                    ->money('Afn'),

                Tables\Columns\TextColumn::make('staff.joining_date')
                    ->label(__('Joining Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable(),
            ])
            ->filters([
                // Date Range Filter for joining_date
                Filter::make('joining_date')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['from'], fn($q, $date) => $q->whereHas('staff', fn($q2) => $q2->whereDate('joining_date', '>=', $date)))
                            ->when($data['to'], fn($q, $date) => $q->whereHas('staff', fn($q2) => $q2->whereDate('joining_date', '<=', $date)));
                    }),

                // Salary Range
                Filter::make('salary_range')
                    ->form([
                        TextInput::make('min')->numeric()->label(__('Min Salary')),
                        TextInput::make('max')->numeric()->label(__('Max Salary')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['min'], fn($q, $min) => $q->whereHas('staff', fn($q2) => $q2->where('basic_salary', '>=', $min)))
                            ->when($data['max'], fn($q, $max) => $q->whereHas('staff', fn($q2) => $q2->where('basic_salary', '<=', $max)));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
