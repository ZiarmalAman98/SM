<?php

namespace App\Filament\Resources\IncomeSourceResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('amount')
                    ->label(__('Amount'))
                    ->required()
                    ->numeric()
                    ->prefix("AF")
                    ->placeholder(__('Enter the amount')),


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
                    ->columnSpanFull()
                    ->placeholder(__('Add a description if necessary')),


                Forms\Components\FileUpload::make('attachment')
                    ->label(__('Attachment'))
                    ->directory('uploads/income_attachments') // File upload directory
                    ->acceptedFileTypes(['application/pdf', 'image/*']) // Accept PDF or image files
                    ->maxSize(4096) // Max size 2 MB
                    ->columnSpanFull()
                    ->placeholder(__('Attach a relevant file')),
            ])->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('source.name')
                    ->label(__('Source'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->html()
                    ->toggleable()
                    ->default('N/A'),

                Tables\Columns\TextColumn::make('attachment')
                    ->label(__('Attachment'))
                    ->url(fn($record) => $record->attachment ? url('storage/' . $record->attachment) : null) // Proper URL handling
                    ->openUrlInNewTab()
                    ->toggleable()
                    ->default(__('No Attachment')),

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
