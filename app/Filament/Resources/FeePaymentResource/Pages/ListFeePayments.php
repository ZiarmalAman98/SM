<?php

namespace App\Filament\Resources\FeePaymentResource\Pages;

use App\Filament\Resources\FeePaymentResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListFeePayments extends ListRecords
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('print_report')
                ->label(__('Print Report'))
                ->icon('heroicon-o-printer')
                ->form([
                    Select::make('user_id')
                        ->label(__('Select Student'))
                        ->options(
                            User::where('type', 'student')
                                ->with('student')
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $admissionNo = optional($user->student)->admission_no ?? 'N/A';
                                    return [
                                        $user->id => "{$admissionNo} - {$user->name}",
                                    ];
                                })
                        )
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return User::where('type', 'student')
                                ->with('student')
                                ->where(function ($query) use ($search) {
                                    $query
                                        ->whereHas('student', function ($q) use ($search) {
                                            $q->where('admission_no', 'like', "%{$search}%");
                                        })
                                        ->orWhere('name', 'like', "%{$search}%");
                                })
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $admissionNo = optional($user->student)->admission_no ?? 'N/A';
                                    return [
                                        $user->id => "{$admissionNo} - {$user->name}",
                                    ];
                                });
                        })
                        ->preload()
                        ->placeholder(__('All Students')),

                    Select::make('fee_type')
                        ->label(__('Fee Type'))
                        ->relationship('feeType', 'name')
                        ->native(false)
                        ->placeholder(__('All Fee Types')),
                    DatePicker::make('from_date')
                        ->default(now()->startOfMonth())
                        ->jalali()
                        ->label(__('From Date'))->required(),

                    DatePicker::make('to_date')
                        ->default(Carbon::now())
                        ->jalali()
                        ->label(__('To Date'))->required(),
                ])
                ->action(function (array $data, $record) {
                    $query = http_build_query([
                        'user_id' => $data['user_id'],
                        'fee_type' => $data['fee_type'],
                        'from_date' => $data['from_date'],
                        'to_date' => $data['to_date'],
                    ]);

                    return redirect()->route('feePayment.print', $query);
                })
                ->modalHeading(__('Select Date Range'))
                ->requiresConfirmation(false),

            Actions\Action::make('classReport')
                ->label(__('Class Monthly Report'))
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn() => static::getResource()::getUrl('class-report'))
                ->color('primary'),
        ];
    }
}
