<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SendEmailResource\Pages;
use App\Models\SendEmail;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TagsInput;

class SendEmailResource extends Resource
{
    protected static ?string $model = SendEmail::class;
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    /* ─────────────────────── Navigation labels ─────────────────────── */

    public static function getNavigationGroup(): string
    {
        return __('Admin');
    }

    public static function getNavigationLabel(): string
    {
        return __('Send Emails');
    }

    public static function getModelLabel(): string
    {
        return __('Send Email');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Send Emails');
    }

    public static function getLabel(): string
    {
        return __('Send Email');
    }

    /* ─────────────────────────── Form ──────────────────────────────── */

    public static function form(Form $form): Form
    {
        $emailTagRules = [
            'required',
            'email', // Laravel e-mail rule for each tag
            'distinct', // no duplicates
        ];

        return $form->schema([
            /* -------- Recipients -------- */
            Section::make(__('Recipients'))
                ->schema([
                    TagsInput::make('to')
                        ->label(__('To (Primary Recipients)'))
                        ->required()
                        ->separator(',') // paste comma-separated list
                        ->placeholder(__('Enter recipient e-mails'))
                        ->nestedRecursiveRules($emailTagRules)
                        ->columnSpanFull(),

                    TagsInput::make('cc')->label(__('CC (Carbon Copy)'))->separator(',')->placeholder(__('Enter CC e-mails'))->nestedRecursiveRules($emailTagRules)->columnSpanFull(),

                    TagsInput::make('bcc')->label(__('BCC (Blind Carbon Copy)'))->separator(',')->placeholder(__('Enter BCC e-mails'))->nestedRecursiveRules($emailTagRules)->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible(),

            /* -------- Email Content -------- */
            Section::make(__('Email Content'))
                ->schema([Forms\Components\TextInput::make('title')->label(__('Subject'))->required()->maxLength(255)->placeholder(__('Enter e-mail subject')), Forms\Components\RichEditor::make('description')->label(__('Message'))->required()->fileAttachmentsDirectory('send-email')->fileAttachmentsVisibility('visible')->columnSpanFull()->placeholder(__('Type your message here…')), Forms\Components\FileUpload::make('attachments')->label(__('Attachments'))->multiple()->downloadable()->openable()->preserveFilenames()->directory('email-attachments')->columnSpanFull()])
                ->collapsible(false),

            /* -------- Settings -------- */
            Section::make(__('Settings'))
                ->schema([Forms\Components\Toggle::make('is_urgent')->label(__('Mark as Urgent'))->inline(false), Forms\Components\Toggle::make('read_receipt')->label(__('Request Read Receipt'))->inline(false)])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    /* ─────────────────────────── Table ─────────────────────────────── */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from')->label(__('Sender'))->searchable()->sortable(),

                Tables\Columns\TextColumn::make('to')
                    ->label(__('To'))
                    ->formatStateUsing(
                        fn(
                            $state, // displayed value
                        ) => is_array($state) ? implode(', ', $state) : $state,
                    )
                    ->searchable()
                    ->limit(30)
                    ->tooltip(
                        fn(
                            $record, // full tooltip
                        ) => is_array($record->to) ? implode(', ', $record->to) : $record->to,
                    ),

                Tables\Columns\TextColumn::make('cc')->label(__('CC'))->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)->searchable()->limit(30)->tooltip(fn($record) => is_array($record->cc) ? implode(', ', $record->cc) : $record->cc)->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('bcc')->label(__('BCC'))->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)->searchable()->limit(30)->tooltip(fn($record) => is_array($record->bcc) ? implode(', ', $record->bcc) : $record->bcc)->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title')->label(__('Subject'))->searchable()->limit(30),

                Tables\Columns\IconColumn::make('is_urgent')->label(__('Urgent'))->boolean()->trueIcon('heroicon-o-exclamation-triangle')->falseIcon('heroicon-o-check'),

                Tables\Columns\TextColumn::make('created_at')->label(__('Sent At'))->dateTime()->sortable(),

                Tables\Columns\TextColumn::make('updated_at')->label(__('Updated At'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\Filter::make('urgent')->label(__('Urgent Emails'))->query(fn($query) => $query->where('is_urgent', true))])
            ->actions([Tables\Actions\EditAction::make()->label(__('Edit')), Tables\Actions\Action::make('resend')->label(__('Resend'))->icon('heroicon-o-arrow-path')->action(fn($record) => $record->sendEmail())])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected'))])->label(__('Bulk Actions'))]);
    }

    /* ───────────────────────── Relations & Pages ───────────────────── */

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSendEmails::route('/'),
            'create' => Pages\CreateSendEmail::route('/create'),
            'edit' => Pages\EditSendEmail::route('/{record}/edit'),
        ];
    }
}