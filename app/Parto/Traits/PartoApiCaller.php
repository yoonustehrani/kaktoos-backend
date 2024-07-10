<?php

namespace App\Parto\Traits;

use App\Exceptions\PartoErrorException;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

trait PartoApiCaller
{
    use PartoAirMethods;
    
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
    public function login(): bool
    {
        try {
            $response = $this->apiCall(uri: 'Authenticate/CreateSession', data: [
                'UserName' => $this->config['username'],
                'Password' => Cache::rememberForever('parto-password', fn() => hash('sha512', $this->config['password'])),
                'OfficeId' => $this->config['office_id']
            ], auth: false);
            if ($response->SessionId) {
                session()->put($this->session_key, [
                    'id' => $response->SessionId,
                    'expires' => now()->addMinutes(14)->addSeconds(30)->getTimestamp()
                ]);
                return true;
            }
            return false;
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
        if ($auth === true && $this->loginExpired() === true) {
            session()->forget($this->session_key);
            if ($this->login()) {
                return $this->apiCall($uri, $data, $auth);
            }
        }
        $http = Http::acceptJson()->connectTimeout(60)->timeout(60)->retry(2);
        if ($auth) {
            $data['SessionId'] = $this->getPartoSession();
        }
        $response = $http->post('https://apidemo.partocrs.com/api/' . $uri, $data);
        if ($response->clientError() || $response->json('Success') === false) {
            throw new PartoErrorException($response->json('Error'));
        }
        if ($response->serverError()) {
            throw new Exception('Parto Server Error');
        }
        return (object) $response->json();
    }
}