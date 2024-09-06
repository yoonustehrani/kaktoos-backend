<?php

namespace App\Models\Parto\Air;

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
}
