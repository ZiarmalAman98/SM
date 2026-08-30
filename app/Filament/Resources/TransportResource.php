<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransportResource\Pages;
use App\Filament\Resources\TransportResource\RelationManagers;
use App\Filament\Resources\TransportResource\RelationManagers\VehicleAssignmentsRelationManager;
use App\Models\Route;
use App\Models\Transport;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Vehicle;
use Filament\Forms\Components\Section;

class TransportResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    // protected static ?string $navigationGroup = 'Default Data';
    protected static ?int $navigationSort = 8;
public static function getNavigationGroup(): string
{
    return __('Default Data');
}

public static function getNavigationLabel(): string
{
    return __('Trucks');
}

public static function getModelLabel(): string
{
    return __('Truck');
}

public static function getPluralModelLabel(): string
{
    return __('Trucks');
}

public static function getLabel(): string
{
    return __('Truck');
}
public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            // Section for Route Information
            Section::make(__('Route Information'))
                ->schema([
                    // Route Name
                    TextInput::make('name')
                        ->label(__('Route Name'))
                        ->required()
                        ->placeholder(__('Enter route name')),

                    Forms\Components\Select::make('branch_id')
                        ->label(__('Branch Name'))
                        ->relationship('branch', 'branch_name')
                        ->searchable()
                        ->required()
                        ->preload(),

                    // Description
                    RichEditor::make('description')
                        ->label(__('Description'))
                        ->nullable()
                        ->fileAttachmentsDirectory('routes')
                        ->placeholder(__('Optional description'))
                        ->columnSpanFull(),  // Full width

                    Toggle::make('status')
                        ->label(__('Is Available'))
                        ->default(true)
                        ->columnSpanFull(),  // Full width
                ])
                ->columns(2), // One column for this section

            // Section for Assigned Vehicles
            Section::make(__('Vehicle Information'))
                ->schema([
                    Repeater::make('vehicleAssignments')
                        ->label(__('Assigned Vehicles'))
                        ->minItems(0)
                        ->relationship('vehicleAssignments')
                        ->schema([
                            // Vehicle Selection with Create Option
                            Select::make('vehicle_id')
                                ->label(__('Vehicle'))
                                ->options(
                                    Vehicle::all()->mapWithKeys(function ($vehicle) {
                                        $status = $vehicle->status ? __('Available') : __('Not Available');
                                        return [
                                            $vehicle->id => "{$vehicle->driver_name} - {$vehicle->vehicle_number} ({$vehicle->type}) - {$status}"
                                        ];
                                    })
                                )->searchable()
                                ->required()
                                ->columnSpan(10)  // Adjusting width
                                ->createOptionForm([
                                    // Vehicle creation form fields
                                    Forms\Components\TextInput::make('vehicle_number')
                                        ->label(__('Vehicle Number'))
                                        ->placeholder(__('Enter new vehicle number'))
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('model')
                                        ->label(__('Vehicle Model'))
                                        ->placeholder(__('Enter new vehicle model'))
                                        ->maxLength(255),

                                    Forms\Components\Select::make('type')
                                        ->label(__('Type'))
                                        ->native(false)
                                        ->options([
                                            'bus' => __('Bus'),
                                            'van' => __('Van'),
                                            'car' => __('Car'),
                                        ])
                                        ->required()
                                        ->placeholder(__('Select vehicle type')),

                                    Forms\Components\TextInput::make('capacity')
                                        ->label(__('Capacity'))
                                        ->placeholder(__('Enter vehicle capacity'))
                                        ->required()
                                        ->numeric(),

                                    Forms\Components\TextInput::make('driver_name')
                                        ->label(__('Driver Name'))
                                        ->placeholder(__('Enter driver name'))
                                        ->nullable(),

                                    Forms\Components\TextInput::make('contact_number')
                                        ->label(__('Contact Number'))
                                        ->placeholder(__('Enter driver contact number'))
                                        ->nullable(),

                                    Toggle::make('status')
                                        ->label(__('Is Available'))
                                        ->default(true)
                                        ->columnSpanFull(),
                                ])
                                ->createOptionUsing(function ($data) {
                                    $vehicle = Vehicle::create([
                                        'vehicle_number' => $data['vehicle_number'],
                                        'type' => $data['type'],
                                        'model' => $data['model'],
                                        'capacity' => $data['capacity'],
                                        'driver_name' => $data['driver_name'],
                                        'contact_number' => $data['contact_number'],
                                        'status' => $data['status'],
                                    ]);
                                    return $vehicle->id;
                                })
                        ])
                        ->collapsible()
                        ->addActionLabel(__('Add Vehicle'))
                ])
        ]);
}


   public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            // Route Name Column
            TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->label(__('Route Name')),

            Tables\Columns\TextColumn::make('branch.branch_name')
                ->label(__('Branch Name'))
                ->sortable()
                ->searchable(),

            TextColumn::make('description')
                ->label(__('Description'))
                ->default(__('Not Available'))
                ->html()
                ->limit(30)
                ->wrap(),

            // Status Column
            IconColumn::make('status')
                ->boolean()
                ->label(__('Available'))
                ->sortable(),

            TextColumn::make('vehicleAssignments')
                ->label(__('Vehicle Assignments'))
                ->formatStateUsing(function ($state, $record) {
                    $vehicleAssignments = $record->vehicleAssignments;

                    if ($vehicleAssignments->isNotEmpty()) {
                        return $vehicleAssignments->map(function ($assignedVehicle) {
                            $vehicle = $assignedVehicle->vehicle;
                            $status = $vehicle->status ? __('Available') : __('Not Available');
                            return "{$vehicle->driver_name} - {$vehicle->vehicle_number} ({$vehicle->type}) - {$status}";
                        })->implode("<br>");
                    }

                    return __('No vehicles assigned');
                })
                ->html(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->label(__('Status'))
                ->native(false)
                ->options([
                    1 => __('Approved'),
                    0 => __('Not Approved'),
                ]),
        ])
        ->defaultSort('created_at', 'desc')
        ->actions([
            Tables\Actions\EditAction::make()
                ->label(__('Edit')),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete')),
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
            'index' => Pages\ListTransports::route('/'),
            'create' => Pages\CreateTransport::route('/create'),
            'edit' => Pages\EditTransport::route('/{record}/edit'),
        ];
    }
}
