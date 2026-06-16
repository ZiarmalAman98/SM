<?php
namespace App\Filament\Resources\BonusResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\BonusResource;
use Illuminate\Routing\Router;


class BonusApiService extends ApiService
{
    protected static string | null $resource = BonusResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
