<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\EmployeeLeaveResource\Pages;
use App\Filament\Teacher\Resources\EmployeeLeaveResource\RelationManagers;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeLeaveResource extends Resource
{
    protected static ?string $model = EmployeeLeave::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('My Account');
    }

    public static function getLabel(): string
    {
        return __('My Leave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('My Leaves');
    }

    public static function getNavigationLabel(): string
    {
        return __('My Leaves');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', auth()->user()->id)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('Start Date'))
                    ->required()
                    ->default(Carbon::now())
                    ->placeholder(__('Select the start date')),

                Forms\Components\DatePicker::make('end_date')
                    ->label(__('End Date'))
                    ->required()
                    ->placeholder(__('Select the end date')),

                Forms\Components\Select::make('leave_type')
                    ->label(__('Leave Type'))
                    ->options([
                        'sick' => __('Sick Leave'),
                        'casual' => __('Casual Leave'),
                        'paid' => __('Paid Leave'),
                        'unpaid' => __('Unpaid Leave'),
                        'maternity' => __('Maternity Leave'),
                        'paternity' => __('Paternity Leave'),
                        'study' => __('Study Leave'),
                        'bereavement' => __('Bereavement Leave'),
                        'emergency' => __('Emergency Leave'),
                        'half_day' => __('Half Day Leave'),
                        'comp_off' => __('Compensatory Off'),
                        'earned' => __('Earned Leave'),
                        'others' => __('Other'),
                    ])
                    ->required()
                    ->native(false)
                    ->searchable(),

                Forms\Components\RichEditor::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->placeholder(__('Enter leave description')),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label(__('Reviewed By'))
                    ->placeholder('-')
                    ->default(__('Pending'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->default(__('Pending'))
                    ->formatStateUsing(fn($record) => is_null($record->approved_by) ? __('Pending') : ($record->status ? __('Approved') : __('Rejected')))
                    ->colors([
                        'gray' => fn($record) => is_null($record->status),
                        'success' => fn($record) => !is_null($record->approved_by) && $record->status,
                        'danger' => fn($record) => !is_null($record->approved_by) && !$record->status,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Request At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEmployeeLeaves::route('/'),
            'create' => Pages\CreateEmployeeLeave::route('/create'),
            'view' => Pages\ViewEmployeeLeave::route('/{record}'),
            'edit' => Pages\EditEmployeeLeave::route('/{record}/edit'),
        ];
    }
}