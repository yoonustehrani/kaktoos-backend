<?php

namespace App\Parto\Domains\Hotel\Builder;

use App\Models\User;

class HotelBookingQueryBuilder extends QueryBuilder
{
    public function __construct(?User $user = null)
    {
        $default = [
            'ClientUniqueId' => null,
            'PhoneNumber' => null,
            'Email' => null,
            'Note' => null,
            'Rooms' => [],
            // 'HotelTransfers' => []
        ];
        $this->query = $default;
        if ($user) {
            $this->setUser($user);
        }
    }

    public function setUser(User $user)
    {
        $this->set('PhoneNumber', $user->phone_number);
        $this->set('Email', $user->email);
        return $this->set('ClientUniqueId', "U{$user->id}");
    }

    public static function newRoom()
    {
        return new HotelRoomBuilder();
    }

    public function addRoom(HotelRoomBuilder $room)
    {
        $this->query['Rooms'][] = $room->get();
        return $this;
    }
}