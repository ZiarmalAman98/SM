<?php
namespace App\Filament\Resources\StudentResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\StudentResource;
use Illuminate\Routing\Router;


class StudentApiService extends ApiService
{
    protected static string | null $resource = StudentResource::class;

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
