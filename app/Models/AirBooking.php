<?php

namespace App\Models;

use App\Models\Parto\Air\Flight;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\PartoRefundMethod;
use App\Purchasable;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirBooking extends Model implements Purchasable
{
    use HasFactory, HasMetaAttribute;
    protected $fillable = ['status', 'meta'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
            'status' => AirQueueStatus::class,
            'refund_type' => PartoRefundMethod::class
        ];
    }

    public function order()
    {
        return $this->morphOne(Order::class, 'purchasable');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function getUri()
    {
        return route('user.bookings.air.show', ['airBooking' => $this->id]);
    }
}
