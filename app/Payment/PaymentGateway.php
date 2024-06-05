<?php

namespace App\Payment;

use App\Payment\Gateways\GatewayMethods;
use App\Payment\Gateways\JibitGateway;
use App\Payment\Traits\PaymentRequest;
use Exception;

class PaymentGateway
{
    /**
     * Create a new class instance.
     */
    public function __construct(public GatewayMethods $gateway) {
        // $this->callback_url = route();
    }
    public function init($amount, $data = [])
    {
        $this->gateway->setAmount($amount);
        foreach ($data as $key => $value) {
            $this->gateway->setRequestItem($key, $value);
        }
    }
    public function requestPurchase()
    {
        return $this->gateway->requestPayment();
    }
    public function redirect()
    {
        if ($this->gateway->redirectUrl) {
            return redirect()->to($this->gateway->redirectUrl);
        }
        throw new Exception('No redirect URL is set');
    }
    public static function getGateways()
    {
        return [
            'jibit' => JibitGateway::class
        ];
    }
    public static function getGatewayClassname(string $name)
    {
        return self::getGateways()[$name] ?? throw new Exception("No gateway with the name $name");
    }
}
