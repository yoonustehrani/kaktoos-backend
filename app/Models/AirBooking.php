<?php

namespace App\Models;

use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Purchasable;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirBooking extends Model implements Purchasable
{
    use HasFactory, HasMetaAttribute;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
            'status' => AirQueueStatus::class
        ];
    }

    public function order()
    {
        return $this->morphOne(Order::class, 'purchasable');
    }

    public function getUri()
    {
        return $this->id;
    }
}
