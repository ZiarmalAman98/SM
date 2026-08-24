<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactDetailResource\Pages;
use App\Models\ContactDetail;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ContactDetailResource extends Resource
{
    protected static ?string $model = ContactDetail::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?int $navigationSort = 3;

    public static function getLabel(): string
    {
        return __('Contact Detail');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contact Details');
    }

    public static function getNavigationLabel(): string
    {
        return __('Contact Details');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reception');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Deleted user'))
                    ->tooltip(fn($record) => $record->user?->name ?? __('Deleted user')),

                TextColumn::make('user.type')
                    ->label(__('User Type'))
                    ->sortable()
                    ->badge()
                    ->searchable()
                    ->placeholder(__('Unknown'))
                    ->formatStateUsing(fn($state) => filled($state) ? __(ucfirst($state)) : __('Unknown')),

                TextColumn::make('phone_number')->label(__('Phone'))->searchable()->tooltip(fn($record) => $record->phone_number),

                BadgeColumn::make('phone_type')
                    ->label(__('Type'))
                    ->colors([
                        'Mobile' => 'success',
                        'Home' => 'primary',
                        'Office' => 'warning',
                        'Other' => 'gray',
                    ])
                    ->sortable()
                    ->formatStateUsing(fn($state) => __($state)),

                BooleanColumn::make('is_primary')->label(__('Primary'))->sortable()->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')->trueColor('success')->falseColor('danger')->tooltip(fn($state) => $state ? __('Primary contact') : __('Secondary contact')),

                TextColumn::make('created_at')->label(__('Created'))->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)->tooltip(fn($record) => $record->created_at->diffForHumans()),

                TextColumn::make('updated_at')->label(__('Updated'))->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)->tooltip(fn($record) => $record->updated_at->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->options(function () {
                        return User::with('roles')
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(function (User $user) {
                                $role = $user->roles->first()?->name;
                                return [
                                    $user->id => $role
                                        ? ucfirst($role) . ": {$user->name}"
                                        : "User: {$user->name}"
                                ];
                            });
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return User::with('roles')
                            ->where(function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhereHas('roles', fn($q) => $q->where('name', 'like', "%{$search}%"));
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function (User $user) {
                                $role = $user->roles->first()?->name;
                                return [
                                    $user->id => $role
                                        ? ucfirst($role) . ": {$user->name}"
                                        : "User: {$user->name}"
                                ];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        if (!$value) return null;

                        $user = User::with('roles')->find($value);
                        if (!$user) return null;

                        $role = $user->roles->first()?->name;
                        return $role
                            ? ucfirst($role) . ": {$user->name}"
                            : "User: {$user->name}";
                    })
                    ->preload()
                    ->placeholder(__('All users')),

                SelectFilter::make('phone_type')
                    ->label(__('Phone Type'))
                    ->options([
                        'Mobile' => __('Mobile'),
                        'Home' => __('Home'),
                        'Office' => __('Office'),
                        'Other' => __('Other'),
                    ])
                    ->searchable()
                    ->placeholder(__('All types')),

                TernaryFilter::make('is_primary')->label(__('Primary Only'))->placeholder(__('All contacts'))->trueLabel(__('Yes'))->falseLabel(__('No'))->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn (ContactDetail $record) => route('contact-details.print', $record))
                    ->openUrlInNewTab(),
                EditAction::make()->tooltip(__('Edit phone')),
                DeleteAction::make()->tooltip(__('Delete phone')),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()->label(__('Delete selected')), ExportBulkAction::make()->label(__('Export selected'))])])
            ->emptyStateHeading(__('No phone numbers found'))
            ->emptyStateDescription(__('Add a new phone number to get started'))
            ->emptyStateActions([Tables\Actions\CreateAction::make()->label(__('Add Phone'))])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListContactDetails::route('/'),
            // 'create' => Pages\CreateContactDetail::route('/create'),
            // 'edit' => Pages\EditContactDetail::route('/{record}/edit'),
        ];
    }
}
