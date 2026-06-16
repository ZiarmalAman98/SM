<?php

namespace App\Filament\Teacher\Resources\EmployeeSalaryResource\Pages;

use App\Filament\Teacher\Resources\EmployeeSalaryResource as ResourcesEmployeeSalaryResource;
use App\Filament\Widgets\TotalSalaryPaidByMonthChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeSalaries extends ListRecords
{
    protected static string $resource = ResourcesEmployeeSalaryResource::class;


}
