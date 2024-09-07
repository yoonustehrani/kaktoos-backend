<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightFareRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $is_general = empty($this['CityPair']);
        return [
            'airline' => $this->when(! $is_general, $this['Airline']),
            'is_general' => $is_general,
            'from' => $this->when(! $is_general, explode('-', $this['CityPair'])[0] ?: null),
            'to' => $this->when(! $is_general, explode('-', $this['CityPair'])[1] ?? null),
            'details' => PartoRuleResource::collection($this['RuleDetails']),
        ];
    }
}