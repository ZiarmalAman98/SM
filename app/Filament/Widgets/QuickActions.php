<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StudentResource;
use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\ParentInvoicePaymentResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickActions extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Add Student'), __('Student Registration'))
                ->description(__('Create a new student record'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->url(StudentResource::getUrl('create'))
                ->color('primary'),
            Stat::make(__('Add Teacher'), __('Teacher Registration'))
                ->description(__('Create a new teacher record'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->url(TeacherResource::getUrl('create'))
                ->color('success'),
            Stat::make(__('Record Payment'), __('Student Fee Payment'))
                ->description(__('Register a student invoice payment'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->url(ParentInvoicePaymentResource::getUrl('create'))
                ->color('warning'),
        ];
    }
}
