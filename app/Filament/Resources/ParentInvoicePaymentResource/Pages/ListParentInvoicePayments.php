<?php

namespace App\Filament\Resources\ParentInvoicePaymentResource\Pages;

use App\Filament\Resources\ParentInvoicePaymentResource;
use App\Models\ParentGuardian;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListParentInvoicePayments extends ListRecords
{
    protected static string $resource = ParentInvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('print_report')
                ->label(__('Print Report'))
                ->icon('heroicon-o-printer')
                ->form([
                    Select::make('parent_guardian_id')
                        ->label(__('Family Code / Parent'))
                        ->options(fn() => ParentGuardian::with('user')
                            ->orderBy('family_code')
                            ->get()
                            ->mapWithKeys(function (ParentGuardian $parentGuardian): array {
                                $name = trim(($parentGuardian->user?->name ?? '') . ' ' . ($parentGuardian->user?->last_name ?? ''));

                                return [$parentGuardian->id => "{$parentGuardian->family_code} - {$name}"];
                            }))
                        ->searchable()
                        ->preload()
                        ->placeholder(__('All Families')),
                    Select::make('payment_method')
                        ->label(__('Payment Method'))
                        ->options([
                            'cash' => __('Cash'),
                            'bank' => __('Bank'),
                            'mobile_money' => __('Mobile Money'),
                            'card' => __('Card'),
                            'other' => __('Other'),
                        ])
                        ->native(false)
                        ->placeholder(__('All Methods')),
                    DatePicker::make('from_date')
                        ->label(__('From Date'))
                        ->jalali()
                        ->default(now()->startOfMonth())
                        ->required(),
                    DatePicker::make('to_date')
                        ->label(__('To Date'))
                        ->jalali()
                        ->default(Carbon::now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('parent-invoice-payments.report', array_filter([
                        'parent_guardian_id' => $data['parent_guardian_id'] ?? null,
                        'payment_method' => $data['payment_method'] ?? null,
                        'from_date' => $data['from_date'] ?? null,
                        'to_date' => $data['to_date'] ?? null,
                    ]));
                })
                ->modalHeading(__('Invoice Payment Report'))
                ->requiresConfirmation(false),
        ];
    }
}
