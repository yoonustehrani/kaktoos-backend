<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartoRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category' => $this['Category'],
            'text' => $this['Rules'],
            'meta' => $this['RuleItemsParsed'] ?? [],
            'dir' => $this['MessageDirection']
        ];
    }
}