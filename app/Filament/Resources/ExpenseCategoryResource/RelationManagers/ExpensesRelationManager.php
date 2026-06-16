<?php

namespace App\Filament\Resources\ExpenseCategoryResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('category_id')
                //     ->label(__('Category'))
                //     ->relationship('category', 'name') // Assuming a relationship with `ExpenseCategory`
                //     ->searchable()
                //     ->required()
                //     ->preload()
                //     ->placeholder(__('Select a category')),

                Forms\Components\TextInput::make('amount')
                    ->label(__('Amount'))
                    ->required()
                    ->numeric()
                    ->prefix('AF')
                    ->placeholder(__('Enter amount')),


                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(Carbon::now())
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\RichEditor::make('description')
                    ->label(__('Description'))
                    ->placeholder(__('Enter description'))
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('invoice')
                    ->label(__('Invoice'))
                    ->directory('uploads/expenses') // Directory for storing invoices
                    ->placeholder(__('Upload invoice'))
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf', 'image/*']),

            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice')
                    ->label(__('Invoice'))
                    ->url(fn($record) => $record->invoice ? url('storage/' . $record->invoice) : null) // Ensures full URL
                    ->default(__('No Invoice Uploaded')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
