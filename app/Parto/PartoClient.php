<?php

namespace App\Parto;

use App\Exceptions\PartoErrorException;
use App\Parto\Domains\FlightService;
use App\Parto\Traits\PartoApiCaller;

class PartoClient
{
    use PartoApiCaller;
    public string $session_key = 'parto-session';
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
    
    public function getCredit()
    {
        try {
            $response = $this->apiCall('Common/CreditBalance');
            return $response;
        } catch (PartoErrorException $error) {
            return $error->getErrorObject();
        }
    }
}
