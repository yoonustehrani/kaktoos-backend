<?php

namespace App\Payment\Gateways;

use App\Payment\IranianCurrency;
use Exception;
use Illuminate\Support\Collection;

interface IPaymentGateway
{

}

abstract class GatewayMethods implements IPaymentGateway {
    public int $amount = 0;
    public string $ref;
    public Collection $requestData;
    public IranianCurrency $currency;
    public string $redirectUrl;
    abstract public function getGatewayName(): string;
    abstract public function config(array $config): void;
    abstract public function requestPayment();
    abstract public function validatePayment();
    public function getCallbackUrl() {
        return route('payment.verify', ['gateway' => $this->getGatewayName()]);
    }
    public function __construct()
    {
        $this->requestData = collect([]);
        if(!isset($this->currency) || ! $this->currency instanceof IranianCurrency)
            throw new Exception($this->getGatewayName() . " has no currency property defined in it");
    }
    public function getCurrency() {
        return $this->currency;
    }
    public function setRequestItem($key, $value)
    {
        $this->requestData->put($key, $value);
    }
    /**
     * @param int $amount amount in Toman (IRT)
     */
    public function setAmount(int $amount) {
        if ($amount <= 0) {
            throw new Exception('Amount must be absolute');
        }
        $this->amount = $amount;
    }
    public function setReferenceId(string $order_id)
    {
        $this->ref = $order_id;
    }
    public function getAmount() {
        return $this->getCurrency() === IranianCurrency::TOMAN
        ? $this->amount
        : $this->amount * 10;
    }
}