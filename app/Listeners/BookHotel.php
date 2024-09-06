<?php

namespace App\Listeners;

use App\Events\Parto\HotelBooked;
use App\Events\Parto\HotelBookingOrderPaid;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Hotel\Builder\HotelPassengerBuilder;
use App\Parto\Enums\HotelQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class BookHotel
{
    public $tries = 3;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HotelBookingOrderPaid $event): void
    {
        // $event->order->load('purchasable');
        /**
         * @var \App\Models\Parto\Hotel\HotelBooking
         */
        $booking = $event->order->purchasable;
        $booking->load('user');
        $offer = Parto::api()->hotel()->checkOffer($booking->meta?->ref)->PricedItinerary;
        $rooms = Parto::hotel()->hotelBooking($booking->user);
        foreach ($booking->meta->rooms as $room) {
            $roomQuery = Parto::hotel()->newHotelRoom();
            foreach ($room->guests as $resident) {
                switch (TravellerPassengerType::tryFrom($resident->type)) {
                    case TravellerPassengerType::Chd:
                        $passenger = HotelPassengerBuilder::child(age: $resident->age, gender: TravellerGender::tryFrom($resident->gender));
                        break;
                    default:
                        $passenger = HotelPassengerBuilder::adult(TravellerGender::tryFrom($resident->gender));
                        break;
                }
                $passenger->setName($resident->first_name, $resident->last_name);
                if ($resident->passport_number ?? false) {
                    $passenger->setPassportNumber($resident->passport_number);
                }
                if($resident->national_id ?? false) {
                    $passenger->setNationalId($resident->national_id);
                }
                $roomQuery->addPassenger($passenger);
            }
            $rooms->addRoom($roomQuery);
        }
        $response = Parto::api()->hotel()->bookHotel($offer['FareSourceCode'], $rooms);
        $booking->fill([
            'status' => HotelQueueStatus::tryFrom($response->Status),
            'parto_unique_id' => $response->UniqueId,
            'payment_time_extendable' => $response->CanExtendPaymentDeadline,
            'vat_number' => $response->VatNumber
        ]);
        try {
            $booking->payment_valid_until = Carbon::parse($response->PaymentDeadline);
        } catch (\Throwable $th) {
            throw $th;
        }
        $booking->meta = [
            'cancellation' => [
                'policies' => Arr::map($response->CancellationPolicies, fn($policy) => [
                    'amount' => intval($policy['Amount']),
                    'currency' => 'IRR',
                    'from_date' => Carbon::parse($policy['FromDate'])->format('Y-m-d H:i:s')
                ]),
                'policy_note_fa' => $response->CancellationPolicyNote,
                'policy_note' => $response->CancellationPolicyNoteEn
            ],
            'remarks' => $response->Remarks
        ];
        $booking->save();
        if ($booking->payment_valid_until->gte(now())) {
            $response = Parto::api()->hotel()->confirmHotelBook($booking->parto_unique_id);
            $booking->fill([
                'status' => HotelQueueStatus::tryFrom($response->Status),
            ]);
            $booking->save();
        }
        HotelBooked::dispatch($booking);
    }
}
