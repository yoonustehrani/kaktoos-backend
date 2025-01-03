{{-- Bottom --}}
<div class="w-full h-1/5 bg-white flex justify-around items-center gap-3 px-4">
    <div class="flex gap-2 text-lg">
        <h1 class="font-bold">@lang('Flight Number')</h1>
        <p>{{ $flight->flight_number }}</p>
    </div>
    <div class="flex gap-2 text-lg">
        <h1 class="font-bold">@lang('Ticket Number')</h1>
        <p>{{ $ticket->ticket_number }}</p>
    </div>
    <div class="flex gap-2 text-lg">
        <h1 class="font-bold">@lang('PNR')</h1>
        <p>{{ $ticket->airline_pnr ?? $flight->airline_pnr }}</p>
    </div>
    <div class="flex gap-2 text-lg">
        <h1 class="font-bold">@lang('Date of issue')</h1>
        <p>{{ jalali($ticket->issued_at)->format('Y/m/d H:i') }}</p>
    </div>
</div>