<?php

namespace App\Filament\Resources\BonusResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\BonusResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\BonusResource\Api\Transformers\BonusTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = BonusResource::class;


    /**
     * Show Bonus
     *
     * @param Request $request
     * @return BonusTransformer
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

        return new BonusTransformer($query);
    }
}
