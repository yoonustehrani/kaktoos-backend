<?php

namespace App\Models;

use App\Models\Parto\Air\Flight;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\AirSearch\AirTripType;
use App\Parto\Domains\Flight\Enums\PartoRefundMethod;
use App\Purchasable;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirBooking extends Model implements Purchasable
{
    use HasFactory, HasMetaAttribute;
    protected $fillable = [
        'status', 'type', 'refund_type', 
        'is_webfare',
        'meta',
        'origin_airport_code',
        'destination_airport_code',
        'journey_begins_at',
        'journey_ends_at',
        'airline_code'
    ];

    protected $hidden = ['parto_unique_id'];
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
            'type' => AirTripType::class,
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

    public function origin_airport()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_code', 'IATA_code');
    }

    public function destination_airport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_code', 'IATA_code');
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_code', 'code');
    }

    public function getUri()
    {
        return route('user.bookings.air.status', ['airBooking' => $this->id]);
    }
}
