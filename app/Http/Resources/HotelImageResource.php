<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group' => $this['Group'] ?? 'uncategorized',
            'href' => $this['Link'],
            'other_links' => [
                'thumbnail' => $this['ThumbnailLink'] ?: null,
                'mobile' => $this['MobileLink'] ?: null
            ],
            'caption' => $this['Caption'],
            'alt' => $this['AltText']
        ];
    }
}
