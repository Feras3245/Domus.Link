<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImmobilieResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = $this->images()->pluck('id')->toArray();
        $title = $this->title()->first(['de', 'en']);
        $shortDescription = $this->shortDescription()->first(['de', 'en']);
        $longDescription = $this->longDescription()->first(['de', 'en']);
        $location = $this->location()->first();
        $units = [];
        foreach ($this->units()->get()->toArray() as $unit) {
            unset($unit['immobilie_id'], $unit['created_at'], $unit['updated_at'], $unit['type_id']);
            array_push($units, $unit);
        }
        return [
            'id'                => $this->id,
            'title'             => $title,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'year'              => $this->year,
            'address'           => $this->address,
            'zip'               => $this->zip,
            'city'              => $location->city,
            'state'             => $location->state,
            'type'              => $this->type,
            'usage'             => $this->usage,
            'hidden'            => $this->hidden,
            'images'            => $images,
            'units'             => $units,
            'stats'             => $this->stats
        ];
    }
}
