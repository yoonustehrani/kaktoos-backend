<?php

namespace App\Parto\Client;

class PartoApi
{
    public function __construct(public array $config)
    {
        
    }
    public function air()
    {
        return new PartoAir($this->config);
    }

    public function hotel()
    {
        return new PartoHotel($this->config);
    }
}