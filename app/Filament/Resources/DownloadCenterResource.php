<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadCenterResource\Pages;
use App\Models\DownloadCenter;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DownloadCenterResource extends Resource
{
    protected static ?string $model = DownloadCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';


public static function getLabel(): string
{
    return __('Download Material');
}

public static function getModelLabel(): string
{
    return __('Download Material');
}

public static function getPluralModelLabel(): string
{
    return __('Download Center');
}

public static function getNavigationLabel(): string
{
    return __('Download Center');
}

public static function getNavigationGroup(): string
{
    return __('Assignments');
}
    public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Section::make()->schema([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('Enter material title')),

                Select::make('type')
                    ->label(__('Material Type'))
                    ->options([
                        'book' => __('Book'),
                        'worksheet' => __('Worksheet'),
                        'presentation' => __('Presentation'),
                        'assignment' => __('Assignment'),
                        'lecture_notes' => __('Lecture Notes'),
                        'reference' => __('Reference Material'),
                        'project' => __('Project'),
                        'other' => __('Other'),
                    ])
                    ->native(false)
                    ->default('book')
                    ->required()
                    ->placeholder(__('Select material type')),

                RichEditor::make('description')
                    ->label(__('Description'))
                    ->placeholder(__('Write a detailed description...'))
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('attachments')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('file')
                    ->label(__('File'))
                    ->directory('downloadCenter')
                    ->disk('public')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'image/*',
                    ])
                    ->maxSize(2048)
                    ->required()
                    ->helperText(__('Maximum file size: 2MB'))
                    ->openable()
                    ->downloadable()
                    ->deletable()
                    ->columnSpanFull()
            ])
            ->description(__('Download Center Form'))
            ->collapsed(false)
            ->columns(2)
        ]);
}

   public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            TextColumn::make('title')
                ->label(__('Title'))
                ->limit(20)
                ->sortable()
                ->searchable()
                ->tooltip(fn($record) => $record->title),

            TextColumn::make('description')
                ->limit(20)
                ->icon('heroicon-m-document-duplicate')
                ->iconPosition('after')
                ->copyable()
                ->copyMessage(__('Description copied'))
                ->copyMessageDuration(1500)
                ->tooltip(
                    fn(TextColumn $column): ?string =>
                    strlen($column->getState()) > $column->getCharacterLimit()
                        ? $column->getState()
                        : null
                )
                ->label(__('Description')),

            TextColumn::make('uploader.name')
                ->label(__('Uploaded By'))
                ->alignCenter()
                ->sortable()
                ->searchable()
                ->placeholder(__('Unknown'))
                ->tooltip(fn($record) => $record->uploader?->name ?? __('Unknown')),

            TextColumn::make('updatedBy.name')
                ->label(__('Updated By'))
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->tooltip(fn($record) => $record->updatedBy?->name ?? __('Not updated')),

            BadgeColumn::make('type')
                ->label(__('Material Type'))
                ->colors([
                    'book' => 'primary',
                    'worksheet' => 'info',
                    'presentation' => 'success',
                    'assignment' => 'warning',
                    'lecture_notes' => 'gray',
                    'reference' => 'indigo',
                    'project' => 'purple',
                    'other' => 'danger',
                ])
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'book' => __('Book'),
                    'worksheet' => __('Worksheet'),
                    'presentation' => __('Presentation'),
                    'assignment' => __('Assignment'),
                    'lecture_notes' => __('Lecture Notes'),
                    'reference' => __('Reference Material'),
                    'project' => __('Project'),
                    'other' => __('Other'),
                    default => __(str($state)->headline()->toString()),
                }),

            ImageColumn::make('file')
                ->label(__('File'))
                ->getStateUsing(
                    fn($record) => $record->file
                        ? (Str::endsWith($record->file, ['.jpg', '.jpeg', '.png', '.webp'])
                            ? Storage::url($record->file)
                            : asset('images/pdf.png'))
                        : asset('images/pdf.png')
                )
                ->circular()
                ->url(fn($record) => $record->file ? Storage::url($record->file) : null)
                ->openUrlInNewTab()
                ->tooltip(__('View file')),

            TextColumn::make('created_at')
                ->label(__('Created At'))
                ->alignCenter()
                ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('type')
                ->label(__('Filter by Type'))
                ->options([
                    'book' => __('Book'),
                    'worksheet' => __('Worksheet'),
                    'presentation' => __('Presentation'),
                    'assignment' => __('Assignment'),
                    'lecture_notes' => __('Lecture Notes'),
                    'reference' => __('Reference Material'),
                    'project' => __('Project'),
                    'other' => __('Other'),
                ])
                ->placeholder(__('All Types')),
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label('')
                ->tooltip(__('View')),
            Tables\Actions\EditAction::make()
                ->label('')
                ->tooltip(__('Edit')),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
            ]),
        ])
        ->emptyStateHeading(__('No materials found'))
        ->emptyStateDescription(__('Create your first download material'))
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->label(__('Add Material')),
        ]);
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloadCenters::route('/'),
            'create' => Pages\CreateDownloadCenter::route('/create'),
            'edit' => Pages\EditDownloadCenter::route('/{record}/edit'),
        ];
    }
}
