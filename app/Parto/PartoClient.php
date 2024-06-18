<?php

namespace App\Parto;

use App\Exceptions\PartoErrorException;
use App\Parto\Domains\Flight\FlightSearch;
use App\Parto\Domains\FlightService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use stdClass;

class PartoClient
{
    const BASE_URL = 'https://apidemo.partocrs.com/api/';
    public string $session_key = 'parto-session';
    /**
     * Create a new class instance.
     */
    public function __construct(protected array $config)
    {
        
    }

    private function getPartoSession(): string
    {
        return session()->get($this->session_key)['id'];
    }

    private function loginExpired()
    {
        if (session()->missing($this->session_key)) {
            return true;
        }
        $timestamp = session()->get($this->session_key)['expires'];
        return Carbon::createFromTimestamp($timestamp)->lt(now());
    }

    public function flight()
    {
        return new FlightService();
    }
    public function searchFlight(FlightSearch $flightSearch): stdClass|null
    {
        try {
            $response = $this->apiCall('Air/AirLowFareSearch', $flightSearch->getQuery());
            return $response;
        } catch (PartoErrorException $error) {
            $errorObject = $error->getErrorObject();
            if ($errorObject->Id === 'Err0103016') {
                return null;
            }
            throw $error;
        }
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

    public function login(): bool
    {
        try {
            $response = $this->apiCall('Authenticate/CreateSession', [
                'UserName' => $this->config['username'],
                'Password' => \Cache::rememberForever('parto-password', fn() => hash('sha512', $this->config['password'])),
                'OfficeId' => $this->config['office_id']
            ], false);
            session()->put($this->session_key, [
                'id' => $response->SessionId,
                'expires' => now()->addMinutes(14)->addSeconds(30)->getTimestamp()
            ]);
            return true;
        } catch (PartoErrorException $error) {
            return $error->getErrorObject();
        }
    }

    public function logout()
    {
        if (session()->missing($this->session_key)) {
            return true;
        }
        try {
            $this->apiCall('Authenticate/EndSession');
        } catch (\Throwable $th) {}
        session()->forget($this->session_key);
        return true;
    }

    public function apiCall(string $uri, array $data = [], $auth = true)
    {
        if ($auth && $this->loginExpired()) {
            session()->forget($this->session_key);
            if ($this->login()) {
                return $this->apiCall($uri, $data, $auth);
            }
        }
        $http = Http::acceptJson()->retry(2)->timeout(60);
        if ($auth) {
            $data['SessionId'] = $this->getPartoSession();
        }
        $response = $http->post(self::BASE_URL . $uri, $data);
        if ($response->clientError() || $response->json('Success') === false) {
            throw new PartoErrorException($response->json('Error'));
        }
        if ($response->serverError()) {
            throw new Exception('PArto Server Error');
        }
        return (object) $response->json();
    }
}
