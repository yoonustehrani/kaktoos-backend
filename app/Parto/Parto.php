<?php

namespace App\Parto;

use App\Parto\Client\PartoApi;
use App\Parto\Client\PartoClient;
use App\Parto\Domains\FlightService;
use App\Parto\Domains\Hotel\HotelServices;

class Parto
{
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s.uP';
    /**
     * Create a new class instance.
     */
    public function __construct(protected array $config)
    {
        
    }

    public function api()
    {
        return new PartoApi($this->config);
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
