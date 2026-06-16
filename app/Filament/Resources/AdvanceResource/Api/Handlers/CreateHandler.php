<?php
namespace App\Filament\Resources\AdvanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AdvanceResource;
use App\Filament\Resources\AdvanceResource\Api\Requests\CreateAdvanceRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AdvanceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Advance
     *
     * @param CreateAdvanceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateAdvanceRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}