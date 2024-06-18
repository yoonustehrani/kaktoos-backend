<?php

namespace App\Http\Resources;

use App\Parto\Domains\Flight\Enums\PartoFareType;
use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use App\Parto\Domains\Flight\Enums\PartoRefundMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'closed' => $this['IsClosed'],
            'refund_type' => str(PartoRefundMethod::tryFrom($this['RefundMethod'])->name)->lower(),
            'passport' => [
                'is_mandatory' => $this['IsPassportMandatory'],
                'issue_date_is_mandatory' => $this['IsPassportIssueDateMandatory'],
                'is_name_with_space' => $this['IsPassportNameWithSpace'],
            ],
            'origin_to_destination' => FlightJourneyResource::collection($this['OriginDestinationOptions']),
            'fare' => [
                'reference' => $this['FareSourceCode'],
                'type' => str(PartoFareType::tryFrom($this['AirItineraryPricingInfo']['FareType'])->name)->lower(),
                'total' => [
                    'amount' => $this['AirItineraryPricingInfo']['ItinTotalFare']['TotalFare'],
                    'currency' => $this['AirItineraryPricingInfo']['ItinTotalFare']['Currency']
                ],
                'breakdown' => FlightFareBreakdownResource::collection($this['AirItineraryPricingInfo']['PtcFareBreakdown'])
            ]
        ];
    }
}
