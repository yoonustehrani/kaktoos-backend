<?php

namespace App\Http\Controllers;

use App\Models\AirBooking;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class TicketController extends Controller
{
    public function index(AirBooking $airBooking)
    {
        abort_if($airBooking->status != AirQueueStatus::Ticketed, 403);
        $airBooking->load('origin_airport', 'destination_airport');
        $airBooking->load(['passengers.tickets', 'flights' => function($query) {
            $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
        }]);
        $view = 'ticket';
        if ($airBooking->origin_airport->country_code  == 'IR' && $airBooking->destination_airport->country_code == 'IR') {
            $view = 'ticket-fa';
        }
        $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
        return view('pdfs.' . $view)
            ->with('passengers', $airBooking->passengers)
            ->with('flights', $airBooking->flights);
        return $view;
        // $response = Http::post('http://pdfrenderer:8082/render', [
        //     'html' => $view->render(), // Render a Blade view
        // ]);
        // $pdf = $response->body();
        // return response($pdf, 200, [
        //     'Content-Type' => 'application/pdf'
        // ]);
        // return Response::streamDownload(function() use($pdf) {
        //     echo $pdf;
        // }, 'ticket-' . $ticketId . '.pdf', [
        //     'Content-Type' => 'application/pdf',
        // ]);
    }
}
