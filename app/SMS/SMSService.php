<?php

namespace App\SMS;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Melipayamak\MelipayamakApi;

class SMSService
{
    protected const BASE_URL = 'https://rest.payamak-panel.com/api/SendSMS/%s';
    protected string|array $to;
    public function __construct(
        protected string $username,
        protected string $password,
        protected string $from
    )
    {
        
    }
    public static function default()
    {
        return new self(
            username: config('services.sms.melli_payamak.username'),
            password: config('services.sms.melli_payamak.password'),
            from: config('services.sms.melli_payamak.from')
        );
    }
    public function from(string $from)
    {
        $this->from = $from;
    }
    public function to(array|string $recipient)
    {
        $this->to = is_array($recipient) ? implode(',', $recipient) : $recipient;
        return $this;
    }
    public function resolveApiCall(string $response)
    {
        return json_decode($response);
    }
    public function send(string $text)
    {
        // try {
        //     /**
        //      * @var \Melipayamak\SmsRest $api
        //      */
        //     $api = $this->driver;
        //     $response = $api->sendByBaseNumber(to: $this->to, text: $text);
        //     $response = $this->resolveApiCall($response);
        //     return $response;
        //     // return $response?->recId;
        // } catch (\Throwable $th) {
        //     throw $th;
        // }
    }
    public function sendPattern(string $patternKey, array $params)
    {
        $url = sprintf(self::BASE_URL, 'BaseServiceNumber');
        if (! Config::has("services.sms.melli_payamak.patterns.{$patternKey}")) {
            throw new Exception("No such pattern: {$patternKey}");
        }
        $reponse = $this->apiCall($url, [
            'bodyId' => config("services.sms.melli_payamak.patterns.{$patternKey}"),
            'text' => implode(';', $params)
        ]);
        if ($reponse['RetStatus'] == 1 && $reponse['StrRetStatus'] == 'Ok') {
            return true;
        }
        return false;
    }
    public function apiCall(string $url, array $data)
    {
        try {
            $http = Http::acceptJson();
            $response = $http->post($url, array_merge([
                'username' => $this->username,
                'password' => $this->password,
                "to" => $this->to
            ], $data));
            return $response->json();
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }
}