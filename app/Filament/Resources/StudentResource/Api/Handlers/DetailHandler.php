<?php

namespace App\Filament\Resources\StudentResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\StudentResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\StudentResource\Api\Transformers\StudentTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = StudentResource::class;


    /**
     * Show Student
     *
     * @param Request $request
     * @return StudentTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new StudentTransformer($query);
    }
}
