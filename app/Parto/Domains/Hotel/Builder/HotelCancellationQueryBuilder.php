<?php

namespace App\Parto\Domains\Hotel\Builder;

class HotelCancellationQueryBuilder extends QueryBuilder
{
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
}