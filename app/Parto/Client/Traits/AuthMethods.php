<?php

namespace App\Parto\Client\Traits;

use App\Exceptions\PartoErrorException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait AuthMethods
{
    protected const AUTH_CACHE_KEY = 'parto-client-session-data';
    protected function login(): bool
    {
        try {
            $response = $this->apiCall(uri: 'Authenticate/CreateSession', data: [
                'UserName' => $this->config['username'],
                'Password' => Cache::rememberForever('parto-password', fn() => hash('sha512', $this->config['password'])),
                'OfficeId' => $this->config['office_id']
            ], auth: false);
            if ($response->SessionId) {
                return Cache::put(self::AUTH_CACHE_KEY, value: $response->SessionId, ttl: now()->addMinutes(14)->addSeconds(30));
            }
            return false;
        } catch (PartoErrorException $error) {
            return $error->getErrorObject();
        }
    }

    private function loginExpired()
    {
        return Cache::missing(self::AUTH_CACHE_KEY);
    }

    protected function logout(): bool
    {
        if (Cache::missing(self::AUTH_CACHE_KEY)) {
            return true;
        }
        try {
            $this->apiCall('Authenticate/EndSession');
        } catch (\Throwable $th) {
            return false;
        }
        return Cache::forget(self::AUTH_CACHE_KEY);
    }
}