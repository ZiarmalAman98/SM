<?php

namespace App\Filament\Resources\TeacherPlanResource\Pages;

use App\Filament\Resources\TeacherPlanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use App\Models\SchoolClass;
use App\Exports\TeacherPlanWordExport;
use Morilog\Jalali\Jalalian;

class ListTeacherPlans extends ListRecords
{
    protected static string $resource = TeacherPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Teacher Plan'),

            Action::make('exportToWord')
                ->label('Export to Word')
                ->icon('heroicon-o-document-arrow-down')
                ->modalHeading('Export Teacher Plan to Word')
                ->form([
                    Forms\Components\Select::make('class_id')
                        ->label('Class')
                        ->options(
                            fn() =>
                            SchoolClass::query()
                                ->orderBy('class_name')
                                ->pluck('class_name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('year')
                        ->label('Year (Solar Hijri)')
                        ->options(function () {
                            $y = Jalalian::fromCarbon(now())->getYear();
                            $years = [];
                            foreach (range($y, $y - 4) as $yy) {
                                $years[(string) $yy] = (string) $yy;
                            }
                            return $years;
                        })
                        ->default(fn() => (string) Jalalian::fromCarbon(now())->getYear())
                        ->required(),

                    Forms\Components\Select::make('month')
                        ->label('Month (Solar Hijri)')
                        ->options([
                            '01' => 'Hamal',
                            '02' => 'Sawar',
                            '03' => 'Jawza',
                            '04' => 'Saratan',
                            '05' => 'Asad',
                            '06' => 'Sunbula',
                            '07' => 'Mizan',
                            '08' => 'Aqrab',
                            '09' => 'Qaws',
                            '10' => 'Jadi',
                            '11' => 'Dalwa',
                            '12' => 'Hoot',
                        ])
                        ->default(fn() => str_pad((string) Jalalian::fromCarbon(now())->getMonth(), 2, '0', STR_PAD_LEFT))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $export = new TeacherPlanWordExport(
                        (int) $data['class_id'],
                        (int) $data['year'],
                        (string) $data['month']
                    );

                    return $export->download(
                        "teacher-plan-class{$data['class_id']}-{$data['year']}-{$data['month']}.docx"
                    );
                }),
        ];
    }
}
