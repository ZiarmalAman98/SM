<?php

namespace App\Filament\Teacher\Resources\EmployeeLeaveResource\Pages;

use App\Filament\Teacher\Resources\EmployeeLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployeeLeaves extends ListRecords
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    // public function getTabs(): array
    // {
    //     return [
    //         'my_leave' => Tab::make(__('My Leaves'))
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', auth()->user()->id)),

    //     ];
    // }
}
