<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class TicketController extends Controller
{
    public function show(string $ticketId)
    {
        return view('pdfs.ticket', [
            'company' => [
                'name' => 'کاکتوس سیر توس',
                'logo' => '',
                'phone_number' => '05131234567'
            ],
            'ticket' => [
                'number' => '123456'
            ]
        ]);
        // $response = Http::post('http://pdfrenderer:8082/render', [
        //     'html' => view('pdfs.ticket', [
        //         'company' => [
        //             'name' => 'کاکتوس سیر توس',
        //             'logo' => '',
        //             'phone_number' => '05131234567'
        //         ],
        //         'ticket' => [
        //             'number' => '123456'
        //         ]
        //     ])->render(), // Render a Blade view
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
