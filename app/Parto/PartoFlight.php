<?php

namespace App\Parto;

class PartoFlight
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public float $pricePerAdult,
        public string $route,
        public string $date
    )
    {
        //
    }
}
