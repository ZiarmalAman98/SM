<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\StudentPaymentResource\Pages;
use App\Filament\Student\Resources\StudentPaymentResource\RelationManagers;
use App\Models\ParentInvoicePayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class StudentPaymentResource extends Resource
{
    protected static ?string $model = ParentInvoicePayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $label = ('My Payments');
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getLabel(): string
    {
        return __('My Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('My Payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('My Payments');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas(
                'invoice.parentGuardian.linkedStudents',
                fn(Builder $query) => $query->where('student_id', Auth::id())
            )
            ->with(['invoice.parentGuardian.user']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label(__('Receipt Number'))
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label(__('Invoice Number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.billing_month')
                    ->label(__('Month'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Paid Amount'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Paid At'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Add related resources if necessary
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentPayments::route('/'),
        ];
    }
}
