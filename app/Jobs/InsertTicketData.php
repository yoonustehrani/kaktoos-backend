<?php

namespace App\Jobs;

use App\Models\AirBooking;
use App\Models\ETicket;
use App\Models\Parto\Air\Flight;
use App\Models\Passenger;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\AirSearch\PartoCabinType;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\PartoPassengerGender;
use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerTitle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InsertTicketData implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public AirBooking $airBooking, public array $parto_results)
    {
        
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $flights = [];
        foreach ($this->parto_results['ReservationItems'] as $flight) {
            array_push($flights, new Flight([
                'flight_number' => $flight['FlightNumber'],
                'airline_pnr' => $flight['AirlinePnr'],
                'departure_airport_code' => $flight['DepartureAirportLocationCode'],
                'departure_terminal' => $flight['DepartureTerminal'],
                'departs_at' => get_carbon_datetime($flight['DepartureDateTime'])->format('Y-m-d H:i:s'),
                'arrival_airport_code' => $flight['ArrivalAirportLocationCode'],
                'arrival_terminal' => $flight['ArrivalTerminal'],
                'arrives_at' => get_carbon_datetime($flight['ArrivalDateTime'])->format('Y-m-d H:i:s'),
                'marketing_airline_code' => $flight['MarketingAirlineCode'], 
                'operating_airline_code' => $flight['OperatingAirlineCode'],
                'is_return' => $flight['IsReturn'],
                'meta' => [
                    'airplane' => [
                        'name' => $flight['AirEquipmentType'],
                        'cabin_type' => FlightCabinType::{PartoCabinType::tryFrom($flight['CabinClassCode'])->name}->value
                    ],
                    'fare_class' => FlightCabinType::tryFrom(str($flight['ResBookDesigCode'])->kebab()->lower())?->name ?? $flight['ResBookDesigCode'],
                    'baggage' => $flight['Baggage'],
                    'journey' => [
                        'duration' => $flight['JourneyDuration'],
                        'duration_in_minutes' => $flight['JourneyDurationPerMinute']
                    ],
                    'stops' => array_map(fn($stop) => [
                        'airport_code' => $stop['ArrivalAirport'],
                        'arrives_at' => get_carbon_datetime($stop['ArrivalDateTime'])->format('Y-m-d H:i:s'),
                        'departs_at' => get_carbon_datetime($stop['DepartureDateTime'])->format('Y-m-d H:i:s')
                    ], $flight['TechnicalStops']),
                    'is_charter' => $flight['IsCharter'],
                ]
            ]));
        }
        $passengers = [];
        foreach ($this->parto_results['CustomerInfoes'] as $customer) {
            $passenger = $customer['Customer'];
            array_push($passengers, [
                'data' => new Passenger([
                    'gender' => str(PartoPassengerGender::tryFrom($passenger['Gender'])->name)->lower(),
                    'type' => TravellerPassengerType::{PartoPassengerType::tryFrom($passenger['PassengerType'])->name}->value,
                    'title' => TravellerTitle::tryFrom($passenger['PaxName']['PassengerTitle'])->name,
                    'first_name' => $passenger['PaxName']['PassengerFirstName'],
                    'middle_name' => $passenger['PaxName']['PassengerMiddleName'],
                    'last_name' => $passenger['PaxName']['PassengerLastName'],
                    'birthdate' => get_carbon_datetime($passenger['DateOfBirth'])->format('Y-m-d'),
                    'country_code' => $passenger['Nationality'],
                    'national_id' => $passenger['NationalId'] ?: null,
                    'passport_number' => $passenger['PassportNumber'] ?: null,
                    'passport_expires_on' => $passenger['PassportExpireDate'] ?: null,
                    'passport_issued_on' => $passenger['PassportIssueDate'] ?: null
                ]),
                'tickets' => array_map(fn($ticket) => new ETicket([
                    'ticket_number' => $ticket['ETicketNumber'],
                    'status' => $ticket['EticketStatus'],
                    'refunded' => $ticket['IsRefunded'],
                    'total_refund' => $ticket['TotalRefund'],
                    'issued_at' => get_carbon_datetime($ticket['DateOfIssue']),
                    'airline_pnr' => $ticket['AirlinePnr']
                ]), $customer['ETicketNumbers'])
            ]);
        }
        DB::transaction(function() use(&$passengers, &$flights) {
            $airBooking = AirBooking::where('id', $this->airBooking->id)
                ->lockForUpdate()
                ->first();
            $airBooking->flights()->delete();
            $airBooking->flights()->saveMany($flights);
            $airBooking->passengers()->delete();
            foreach ($passengers as $passenger_array) {
                $passenger = $airBooking->passengers()->save($passenger_array['data']);
                $passenger->tickets()->saveMany($passenger_array['tickets']);
            }
        });
    }
}
