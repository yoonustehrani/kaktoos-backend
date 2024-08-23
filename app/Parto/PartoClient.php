<?php

namespace App\Parto;

use App\Exceptions\PartoErrorException;
use App\Parto\Domains\FlightService;
use App\Parto\Domains\Hotel\HotelServices;
use App\Parto\Traits\PartoApiCaller;

class PartoClient
{
    use PartoApiCaller;
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s.uP';
    /**
     * Create a new class instance.
     */
    public function __construct(protected array $config)
    {
        
    }

    public static function flight()
    {
        return new FlightService();
    }

    public static function hotel()
    {
        return new HotelServices();
    }
}
