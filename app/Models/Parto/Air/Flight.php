<?php

namespace App\Models\Parto\Air;

use App\Models\Airline;
use App\Models\Airport;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory, HasMetaAttribute;

    protected $fillable = [
        'flight_number', 'airline_pnr', 
        'marketing_airline_code', 'operating_airline_code',
        'departure_airport_code', 'departure_terminal',  'departs_at',
        'arrival_airport_code', 'arrival_terminal', 'arrives_at',
        'meta', 'is_return'
    ];

    public function casts()
    {
        return [
            'arrives_at' => 'datetime',
            'departs_at' => 'datetime'
        ];
    }

    public function arrival_airport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_code', 'IATA_code');
    }

    public function departure_airport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_code', 'IATA_code');
    }

    public function marketing_airline()
    {
        return $this->belongsTo(Airline::class, 'marketing_airline_code', 'code');
    }

    public function operating_airline()
    {
        return $this->belongsTo(Airline::class, 'operating_airline_code', 'code');
    }
}
