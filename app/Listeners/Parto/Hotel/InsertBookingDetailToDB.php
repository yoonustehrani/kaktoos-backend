<?php

namespace App\Listeners\Parto\Hotel;

use App\Events\Parto\HotelBooked;
use App\Http\Resources\PartoHotelPolicyResource;
use App\Models\HotelBookedRoom;
use App\Models\HotelGuest;
use App\Models\HotelResident;
use App\Parto\Enums\HotelQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class InsertBookingDetailToDB
{
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
    public function handle(HotelBooked $event): void
    {
        $response = Parto::api()->hotel()->getBookingData($event->hotelBooking->parto_unique_id, $event->hotelBooking->user_id);
        try {
            DB::beginTransaction();
            $event->hotelBooking->fill([
                'status' => HotelQueueStatus::Booked, 
                // TODO: parto should answer why the status still is 20
                // HotelQueueStatus::tryFrom($response->Status) == HotelQueueStatus::Confirm ?  : HotelQueueStatus::tryFrom($response->Status),
                'vat_number' => $response->VatNumber,
                'supplier' => $response->SupplierName
            ]);
            $event->hotelBooking->meta = array_merge($event->hotelBooking->meta, [
                'hotel_policy' => (new PartoHotelPolicyResource($response->HotelPolicy))->toArray(new Request()),
                'check_in' => $response->CheckIn,
                'check_out' => $response->CheckOut,
                'amenties' => $response->Amenities,
                'confirmation_number' => $response->HotelConfirmationNo
            ]);
            $event->hotelBooking->save();
            foreach ($response->Rooms as $room) {
                $bookedRoom = $event->hotelBooking->rooms()->save(new HotelBookedRoom([
                    'room_archive_id' => $room['RoomArchiveId'],
                    'name' => $room['Name'],
                    'meal_type' => $room['MealType'],
                    'meta' => [
                        'sharing_bedding' => $room['SharingBedding'],
                        'bed_groups' => $room['BedGroups'],
                        'early_check_in' => $room['HotelRoomEarlyCheckin'],
                        'early_check_out' => $room['HotelRoomLateCheckout'],
                    ]
                ]));
                foreach ($room['Passengers'] as $resident) {
                    $bookedRoom->guests()->save(new HotelGuest([
                        'first_name' => $resident['FirstName'],
                        'last_name' => $resident['LastName'],
                        'type' => $resident['PassengerType'],
                        'title' => $resident['PassengerTitle'],
                        'age' => $resident['ChildAge'],
                        'national_id' => $resident['NationalId'],
                        'passport_number' => $resident['PassportNumber'],
                    ]));
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
