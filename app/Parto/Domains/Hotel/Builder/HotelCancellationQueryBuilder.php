<?php

namespace App\Parto\Domains\Hotel\Builder;

use Illuminate\Support\Arr;

class HotelCancellationQueryBuilder
{
    protected $query = [];

    public function __construct()
    {
        $default = [
            'CancelActor' => 'Passenger',
            'RefundPaymentMode' => 'Credit',
        ];
        $this->query = $default;
    }

    public function asPassenger()
    {
        return $this->set('CancelActor', 'Passenger');
    }

    public function asSupplier()
    {
        return $this->set('CancelActor', 'Passenger');
    }

    public function toCredit()
    {
        return $this->set('RefundPaymentMode', 'Credit');
    }

    public function toBankAccount()
    {
        return $this->set('RefundPaymentMode', 'BankAccount');
    }

    protected function set(string $key, mixed $value)
    {
        Arr::set($this->query, $key, $value);
        return $this;
    }

    public function get(): array
    {
        return Arr::undot($this->query);
    }
}