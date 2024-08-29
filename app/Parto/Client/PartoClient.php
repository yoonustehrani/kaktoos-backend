<?php

namespace App\Parto\Client;

use Exception;
use App\Exceptions\PartoErrorException;
use App\Parto\Client\Traits\AuthMethods;
use App\Parto\Client\Traits\PartoAirMethods;
use App\Parto\Client\Traits\PartoHotelMethods;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;

class PartoClient
{
    use AuthMethods;
    
    public function __construct(protected array $config)
    {
        
    }

    private function getPartoSession(): string
    {
        return Cache::get(self::AUTH_CACHE_KEY);
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

    protected function apiCall(string $uri, array $data = [], $auth = true)
    {
        if ($auth === true && $this->loginExpired() === true) {
            if ($this->login()) {
                return $this->apiCall($uri, $data, $auth);
            }
        }
        $http = Http::acceptJson()->timeout(60)->asJson()->withHeader('Accept-Encoding', 'gzip')->withOptions([
            'decode_content' => 'gzip',
            'verify' => false
        ]);
        if ($auth) {
            $data['SessionId'] = $this->getPartoSession();
        }
        $response = $http->post($this->config['endpoint'] . $uri, $data);
        if ($response->clientError() || $response->json('Success') === false) {
            $error = new PartoErrorException($response->json('Error'));
            if ($error->id === 'Err0102008') {
                // $this->logout();
                Cache::forget(self::AUTH_CACHE_KEY);
                return $this->apiCall($uri, $data, $auth);
            }
        }
        if ($response->serverError()) {
            throw new Exception('Parto Server Error');
        }
        // Log the response details
        // Log::info('Response:', [
        //     'status' => $response->status(),
        //     'headers' => $response->headers(),
        //     'body' => $response->body(),
        // ]);
        return (object) $response->json();
    }
}
