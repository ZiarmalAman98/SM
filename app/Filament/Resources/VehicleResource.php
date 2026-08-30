<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TextFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Section;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    // protected static ?string $navigationGroup = 'Default Data';
    protected static ?int $navigationSort = 9;
public static function getNavigationGroup(): string
{
    return __('Default Data');
}

public static function getNavigationLabel(): string
{
    return __('Vehicles');
}

public static function getModelLabel(): string
{
    return __('Vehicle');
}

public static function getPluralModelLabel(): string
{
    return __('Vehicles');
}

public static function getLabel(): string
{
    return __('Vehicle');
}
public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            // General Information Section
            Section::make(__('Vehicle Information'))
                ->description(__('Enter vehicle details below.'))
                ->schema([
                    TextInput::make('vehicle_number')
                        ->label(__('Vehicle Number'))
                        ->required()
                        ->unique('vehicles', 'vehicle_number', ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder(__('Enter vehicle number'))
                        ->columnSpan(6),

                    TextInput::make('model')
                        ->label(__('Model'))
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder(__('Enter vehicle model'))
                        ->columnSpan(6),

                    Select::make('type')
                        ->label(__('Vehicle Type'))
                        ->native(false)
                        ->options([
                            'bus' => __('Bus'),
                            'van' => __('Van'),
                            'car' => __('Car'),
                        ])
                        ->required()
                        ->placeholder(__('Select vehicle type'))
                        ->columnSpan(6),
                ])
                ->columns(2),

            // Driver Information Section
            Section::make(__('Driver Information'))
                ->description(__('Provide driver details.'))
                ->schema([
                    TextInput::make('driver_name')
                        ->label(__('Driver Name'))
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder(__('Enter driver name'))
                        ->columnSpan(6),

                    TextInput::make('contact_number')
                        ->label(__('Contact Number'))
                        ->nullable()
                        ->maxLength(15)
                        ->placeholder(__('Enter driver contact number'))
                        ->columnSpan(6),
                ])
                ->columns(2),

            // Capacity and Status Section
            Section::make(__('Capacity & Availability'))
                ->description(__('Vehicle capacity and availability status.'))
                ->schema([
                    TextInput::make('capacity')
                        ->label(__('Capacity'))
                        ->numeric()
                        ->nullable()
                        ->placeholder(__('Enter capacity'))
                        ->columnSpan(6),

                    Toggle::make('status')
                        ->label(__('Is Available'))
                        ->default(true)
                        ->columnSpan(6),
                ])
                ->columns(2),
        ]);
}


   public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            TextColumn::make('vehicle_number')
                ->label(__('Vehicle Number'))
                ->sortable()
                ->searchable(),

            TextColumn::make('model')
                ->label(__('Model'))
                ->default(__('N/A'))
                ->sortable(),

            TextColumn::make('type')
                ->label(__('Vehicle Type'))
                ->sortable(),

            TextColumn::make('capacity')
                ->label(__('Capacity'))
                ->sortable()
                ->default(__('N/A')),

            TextColumn::make('driver_name')
                ->label(__('Driver Name'))
                ->sortable()
                ->default(__('N/A')),

            IconColumn::make('status')
                ->boolean()
                ->label(__('Available'))
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('type')
                ->label(__('Vehicle Type'))
                ->options([
                    'bus' => __('Bus'),
                    'van' => __('Van'),
                    'car' => __('Car'),
                ])
                ->native(false)
                ->placeholder(__('Select vehicle type')),

            SelectFilter::make('status')
                ->label(__('Availability'))
                ->options([
                    1 => __('Available'),
                    0 => __('Not Available'),
                ])
                ->native(false)
                ->placeholder(__('Select availability status')),
        ])
        ->defaultSort('created_at', 'desc')
        ->actions([
            EditAction::make()
                ->label(__('Edit')),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
