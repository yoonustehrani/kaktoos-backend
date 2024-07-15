<?php

namespace App\Parto;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Parto\Domains\FlightService flight()
 * @method static mixed getFareRule(string $fareSourceCode)
 * @method static mixed getBaggageRule(string $fareSourceCode)
 * @method static mixed revalidate(string $fareSourceCode)
 * @method static mixed flightBook(\App\Parto\Domains\Flight\FlightBook\FlightBook $flightBook)
 *
 * @see \App\Parto\PartoClient
 *
 * @mixin \Illuminate\Cache\Repository
 */
class Parto extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'parto';
    }
}
