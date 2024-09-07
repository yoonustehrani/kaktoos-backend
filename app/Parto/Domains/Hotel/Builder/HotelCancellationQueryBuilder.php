<?php

namespace App\Parto\Domains\Hotel\Builder;

class HotelCancellationQueryBuilder extends QueryBuilder
{
    public function __construct()
    {
        $default = [
            'CancelActor' => 1,
            'RefundPaymentMode' => 0,
        ];
        $this->query = $default;
    }

    public function asPassenger()
    {
        return $this->set('CancelActor', 0);
    }

    public function asSupplier()
    {
        return $this->set('CancelActor', 1);
    }

    public function toCredit()
    {
        return $this->set('RefundPaymentMode', 1);
    }

    public function toBankAccount()
    {
        return $this->set('RefundPaymentMode', 2);
    }
}