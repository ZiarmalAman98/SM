<?php
namespace App\Filament\Resources\StudentResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Api\Requests\CreateStudentRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = StudentResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Student
     *
     * @param CreateStudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateStudentRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}