<div class="h-full w-1/4 bg-white text-gray-800 border-gray-600 border-dashed flex flex-col justify-between items-center py-2 {{ app()->isLocale('fa') ? 'border-r-[3px]' : 'border-l-[3px]' }}">
    <div class="ml-6 p-3 flex items-center justify-center gap-2">
        <img class="w-10 h-10" width="48px" height="48px" src="{{ asset($flight->operating_airline->logo) }}" alt="">
        <h3 class="font-bold text-xl">{{ name_fa($flight->operating_airline) }}</h3>
    </div>
    <div class="px-3 flex flex-col gap-3">
        <x-ticket.side.detail icon="passenger" :title="__('Passenger')">{{ str($passenger->full_name)->upper() }}</x-ticket.side.detail>
        <x-ticket.side.detail icon="passenger" :title="__('Passenger Type')">{{ __($passenger->gender) }}،&nbsp;{{ __($passenger->type) }}</x-ticket.side.detail>
        @unless ($passenger->type == 'infant')
            <x-ticket.side.detail icon="baggage" :title="__('Baggage')">{{ $flight->meta->baggage }}</x-ticket.side.detail>
        @endunless
        <x-ticket.side.detail icon="location" :title="__('Departure')">
            <img class="w-5 h-5 mx-1 inline rounded-full" height="20px" width="20px" src="{{ asset('storage/images/flags/1x1/' . str($flight->departure_airport->country_code)->lower() . '.svg') }}" alt="">
            {{ name_fa($flight->departure_airport, 'city_name') }}
            @lang(',')
            {{ name_fa($flight->departure_airport->country) }}
        </x-ticket.side.detail>
        <x-ticket.side.detail icon="location" :title="__('Arrival')">
            <img class="w-5 h-5 mx-1 inline rounded-full" height="20px" width="20px" src="{{ asset('storage/images/flags/1x1/' . str($flight->arrival_airport->country_code)->lower() . '.svg') }}" alt="">
            {{ name_fa($flight->arrival_airport, 'city_name') }}
            @lang(',')
            {{ name_fa($flight->arrival_airport->country) }}
        </x-ticket.side.detail>
    </div>
    <div class="bg-gray-300 w-full flex justify-between items-center gap-1 px-3 py-2">
        <p class="text-center text-xl">
            <strong class="bg-[#173F28] inline-block text-gray-100 px-2">@lang('Flight')</strong>
            <br><span class="font-mono">{{ $flight->flight_number }}</span>
        </p>
        <p class="text-4xl font-bold">{{ $flight->departure_airport_code }}</p>
        <svg class="w-12 h-12 {{ app()->isLocale('fa') ? 'rotate-180' : '' }}" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 510 510" style="enable-background:new 0 0 510 510;" xml:space="preserve"><g><g><path d="M510,255c0-20.4-17.85-38.25-38.25-38.25H331.5L204,12.75h-51l63.75,204H76.5l-38.25-51H0L25.5,255L0,344.25h38.25 l38.25-51h140.25l-63.75,204h51l127.5-204h140.25C492.15,293.25,510,275.4,510,255z"/></g></g></svg>
        <p class="text-4xl font-bold">{{ $flight->arrival_airport_code }}</p>
    </div>
    <div class="w-full flex items-center justify-center">
        <img class="h-16 w-auto" src="{{ asset('images/logo.png') }}" alt="">
    </div>
</div>