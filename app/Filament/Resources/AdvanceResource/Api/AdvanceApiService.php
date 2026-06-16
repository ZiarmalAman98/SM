<?php
namespace App\Filament\Resources\AdvanceResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AdvanceResource;
use Illuminate\Routing\Router;


class AdvanceApiService extends ApiService
{
    protected static string | null $resource = AdvanceResource::class;

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
