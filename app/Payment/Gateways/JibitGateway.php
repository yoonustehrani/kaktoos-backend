<?php

namespace App\Payment\Gateways;

use App\Payment\Interfaces\IPaymentGateway;
use App\Payment\IranianCurrency;
use App\Payment\Traits\JibitToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JibitGateway extends GatewayMethods
{
    use JibitToken;
    private string $apiKey;
    private string $secret;
    public const BASE_URL = 'https://napi.jibit.ir/ppg/v3';
    public $accessToken;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->currency = IranianCurrency::RIAL;
        parent::__construct();
        $this->requestData->put('callbackUrl', $this->getCallbackUrl());
    }
    public function config(array $config): void
    {
        if (! $config['apiKey'] || ! $config['secret']) {
            throw 'Bad config for ' . $this->getGatewayName();
        }
        $this->apiKey = $config['apiKey'];
        $this->secret = $config['secret'];
    }
    public function getGatewayName(): string
    {
        return 'jibit';
    }
    public function requestPayment()
    {
        $data = [
            'amount' => $this->getAmount(),
            'clientReferenceNumber' => $this->ref,
            'currency' => 'IRR',
            'userIdentifier' => auth()->user()?->id ?? null,
            'description' => $this->requestData['description'] ?? '',
            'callbackUrl' => $this->requestData['callbackUrl']
        ];
        $result = $this->apiCall(url: '/purchases', data: $data, withAuth: true);
        if (is_array($result) && isset($result['pspSwitchingUrl']) ) {
            $this->redirectUrl = $result['pspSwitchingUrl'];
            $this->purchase_id = $result['purchaseIdStr'];
            return true;
        }
        throw new Exception('Jibi error');
    }
    public function validatePayment(Request $request)
    {
        $url = '/purchases/' . $request->input('purchaseId') . '/verify';
        $result = $this->apiCall(url: $url, data: [], withAuth: true, method: 'POST');
        // if (condition) {
        //     # code...
        // }
        return $result;
    }

    /**
     * @param $id
     * @return bool|mixed|string
     * @throws Exception
     */
    public function getOrderById($purchaseId)
    {
        return  $this->apiCall(url: '/purchases?purchaseId=' . $purchaseId, data: [], withAuth: true, method: 'GET');

    }

    /**
     * @param $url
     * @param $arrayData
     * @param bool $haveAuth
     * @param int $try
     * @param string $method
     * @return bool|mixed|string
     * @throws Exception
     */
    public function apiCall($url, $data = [], $withAuth = false, $retries = 0, $method = 'POST')
    {
        $http = Http::retry($retries, 100);
        $http->accept('application/json');
        if ($withAuth) {
            if (is_null($this->getAccessToken())) {
                $this->generateToken(true);
            }
            $http->withToken($this->getAccessToken());
        }
        $response = $method == 'POST' ? $http->post(self::BASE_URL . $url, $data) : $http->get(self::BASE_URL . $url);
        if ($response->clientError()) {
            $errors = collect($response->json('errors'));
            $codes = $errors->pluck('code');
            if ($codes->contains('security.auth_required')) {
                $this->generateToken(true);
                return $this->apiCall($url, $data, $withAuth, $retries, $method);
            }
            return $errors->toArray();
        }
        if ($response->status() >= 500) {
            throw new Exception('Jibit Server Error');
        }
        return $response->json();

    }
}
