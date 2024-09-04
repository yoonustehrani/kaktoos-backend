<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PartoHotelOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ref' => $this['FareSourceCode'],
            'amenties' => $this['Amenities'],
            'available_room' => $this['AvailableRoom'],
            'net_rate' => [
                'amount' => $this['NetRate'],
                'currency' => $this['Currency']
            ],
            'user_rooms' => array_map(function(array $room) {
                return [
                    'id' => $room['RoomId'],
                    'name' => $room['Name'],
                    'guests' => [
                        'adults' => $room['AdultCount'],
                        'children' => $room['ChildCount'],
                        'children_ages' => $room['ChildAges']
                    ],
                    'meal_type' => $room['MealType'],
                    'bed_groups' => $room['BedGroups']
                ];
            }, $this['Rooms']),
            'hotel_policy' => new PartoHotelPolicyResource($this['HotelPolicy']),
            'cancellation_policies' => array_map(function(array $policy) {
                return [
                    'amount' => $policy['Amount'],
                    'percentage' => round(($policy['Amount'] / $this['NetRate']) * 100, 1),
                    'from_date' => Carbon::parse($policy['FromDate'])->format('Y-m-d'),
                    'currency' => $this['Currency']
                ];
            }, $this['CancellationPolicies']),
            'refundable' => ! $this['NonRefundable'],
            'refund_type' => $this['HotelRefundType'],
            'payment_deadline' => Carbon::parse($this['PaymentDeadline'])->format('Y-m-d H:i:s'),
        ];
    }
}
