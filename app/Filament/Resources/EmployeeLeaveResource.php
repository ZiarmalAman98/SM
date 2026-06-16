<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeLeaveResource\Pages;
use App\Filament\Resources\EmployeeLeaveResource\RelationManagers;
use App\Models\EmployeeLeave;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\ActionSize;
use Morilog\Jalali\Jalalian;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EmployeeLeaveResource extends Resource
{
    protected static ?string $model = EmployeeLeave::class;
    protected static ?int $navigationSort = 2;
    public static function getLabel(): string
    {
        return __('Employee Leave');
    }

    public static function getModelLabel(): string
    {
        return __('Employee Leave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Employee Leaves');
    }

    public static function getNavigationLabel(): string
    {
        return __('Employee Leaves');
    }

    public static function getNavigationGroup(): string
    {
        return __('Account Management');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();
    }







    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->label(__('Employee'))
                        ->relationship('user', 'name')
                        ->searchable(['name', 'email'])
                        ->required()
                        ->preload()
                        ->placeholder(__('Select an Employee')),

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

                    Forms\Components\DatePicker::make('start_date')
                        ->label(__('Start Date'))
                        ->jalali()  // Enable Jalali calendar
                        ->displayFormat('Y/m/d')  // Display format (e.g., 1402/05/15)
                        ->locale('fa')  // Persian/Farsi locale
                        ->required()
                        ->placeholder(__('Select the start date')),

                    Forms\Components\DatePicker::make('end_date')
                        ->label(__('End Date'))
                        ->jalali()  // Enable Jalali calendar
                        ->displayFormat('Y/m/d')  // Display format
                        ->locale('fa')  // Persian/Farsi locale
                        ->required()
                        ->placeholder(__('Select the end date')),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->columnSpanFull()
                        ->placeholder(__('Enter leave description')),

                    Forms\Components\Toggle::make('status')
                        ->label(__('Approved'))
                        ->default(false),
                ])
                    ->description(__('Employee Leave Form'))
                    ->collapsed(false)
                    ->columns(2)
            ])
            ->columns(2);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Employee'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('leave_type')
                    ->label(__('Type'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : __('N/A'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label(__('Reviewed By'))
                    ->placeholder('-')
                    ->default(__('N/A'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Approved'))
                    ->badge()
                    ->formatStateUsing(fn($record) => is_null($record->approved_by) ? __('Pending') : ($record->status ? __('Approved') : __('Rejected')))
                    ->colors([
                        'gray' => fn($record) => is_null($record->approved_by),
                        'success' => fn($record) => !is_null($record->approved_by) && $record->status,
                        'danger' => fn($record) => !is_null($record->approved_by) && !$record->status,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label(__('View')),
                    Tables\Actions\EditAction::make()
                        ->label(__('Edit')),
                    Tables\Actions\Action::make('accept')
                        ->label(__('Accept'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'status' => true,
                                'approved_by' => Auth::id(),
                            ]);
                            Notification::make()
                                ->title(__('Your Leave Request Has Been Approved'))
                                ->body(__('Congratulations! Your leave request has been approved.'))
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->label(__('Reject'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'status' => false,
                                'approved_by' => Auth::id(),
                            ]);
                            Notification::make()
                                ->title(__('Your Leave Request Has Been Rejected'))
                                ->body(__('Unfortunately, your leave request was not approved.'))
                                ->danger()
                                ->send();
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('primary')
                    ->size(ActionSize::Small)
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
                ExportBulkAction::make()
                    ->label(__('Export Selected')),
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
