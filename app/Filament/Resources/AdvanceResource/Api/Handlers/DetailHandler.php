<?php

namespace App\Filament\Resources\AdvanceResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AdvanceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AdvanceResource\Api\Transformers\AdvanceTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AdvanceResource::class;


    /**
     * Show Advance
     *
     * @param Request $request
     * @return AdvanceTransformer
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

        return new AdvanceTransformer($query);
    }
}
