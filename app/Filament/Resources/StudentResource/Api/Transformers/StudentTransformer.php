<?php
namespace App\Filament\Resources\StudentResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

/**
 * @property Student $resource
 */
class StudentTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
