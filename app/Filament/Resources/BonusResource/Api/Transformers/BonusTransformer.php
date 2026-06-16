<?php
namespace App\Filament\Resources\BonusResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Bonus;

/**
 * @property Bonus $resource
 */
class BonusTransformer extends JsonResource
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
