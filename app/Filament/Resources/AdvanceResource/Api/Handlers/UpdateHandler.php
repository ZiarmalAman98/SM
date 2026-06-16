<?php
namespace App\Filament\Resources\AdvanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AdvanceResource;
use App\Filament\Resources\AdvanceResource\Api\Requests\UpdateAdvanceRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = AdvanceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Advance
     *
     * @param UpdateAdvanceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateAdvanceRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}