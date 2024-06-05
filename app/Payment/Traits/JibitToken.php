<?php

namespace App\Payment\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;

trait JibitToken
{
    private $accessToken_key = 'jibit:accessToken';
    private $refreshToken_key = 'jibit:refreshToken';
    /**
     * @param bool $isForce
     * @return string
     * @throws Exception
     */
    public function generateToken($isForce = false)
    {
        if ($isForce === false && Cache::has($this->accessToken_key)) {
            return;
        } else if (Cache::has($this->refreshToken_key)) {
            $refreshToken = $this->refreshTokens();
            if (! $refreshToken) {
                return $this->generateNewToken();
            }
        } else {
            return $this->generateNewToken();
        }
        throw new Exception('unExcepted Err in generateToken.');
    }

    public function refreshTokens()
    {
        $data = [
            'accessToken' => str_replace('Bearer ', '', $this->cache->retrieve('accessToken')),
            'refreshToken' => $this->cache->retrieve('refreshToken'),
        ];
        $result = $this->apiCall(url: '/tokens/refresh', data: $data);
        if (empty($result['accessToken'])) {
            return false;
        }
        if (!empty($result['accessToken'])) {
            $this->setAccessToken($result['accessToken']);
            $this->setRefreshToken($result['refreshToken']);
            return true;
        }
        return false;
    }

    public function setAccessToken($accessToken): bool
    {
        return Cache::put($this->accessToken_key, $accessToken, 24 * 60 * 60 - 60);
    }

    public function setRefreshToken($refreshToken): bool
    {
        return Cache::put($this->refreshToken_key, $refreshToken, 48 * 60 * 60 - 60);
    }
    /**
     * @return string
     */
    public function getAccessToken(): string|null
    {
        return Cache::get($this->accessToken_key);
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string|null
    {
        return Cache::get($this->refreshToken_key);
    }

    private function generateNewToken()
    {
        $response = $this->apiCall(url: '/tokens', data: [
            'apiKey' => $this->apiKey,
            'secretKey' => $this->secret
        ]);
        if (empty($response['accessToken'])) {
            throw new Exception ('Err in generating new token.');
        }
        if (!empty($response['accessToken'])) {
            $this->setAccessToken($response['accessToken']);
            $this->setRefreshToken($response['refreshToken']);
            return true;
        }
        throw new Exception('unExcepted Err in generateNewToken.');
    }

}