<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartoHotelPolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'min_age' => $this['MinAge'],
            'begin_time' => $this['BeginTime'],
            'end_time' => $this['EndTime'],
            'checkout_time' => $this['CheckOutTime'],
            'instructions' => $this['Instructions'],
            'special_instructions' => $this['SpecialInstructions'],
            'mandatory_fee' => $this['MandatoryFee'],
            'optional_fee' => $this['OptionalFee'],
            'notice' => $this['KnowBeforeYouGo'],
            'payment_detail' => $this['PaymentDetail'],
            'license_number' => $this['LicenseNumber'],
            'key_collection_info' => $this['KeyCollectionInfo'],
            'pet' => array_map(fn($item) => $item['name'], $this['PetAttribiute']),
            'fa' => [
                'instructions' => $this['InstructionsFa'],
                'special_instructions' => $this['SpecialInstructionsFa'],
                'child' => $this['ChildPolicyDescriptionFa'],
                'single_woman' => $this['SingleWomanDescriptionFa']
            ],
        ];
    }
}
