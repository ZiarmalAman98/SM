<?php
namespace App\Filament\Resources\AdvanceResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Advance;

/**
 * @property Advance $resource
 */
class AdvanceTransformer extends JsonResource
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
