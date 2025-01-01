<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>بلیط پرواز خارجی</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    {{-- <style>{{ file_get_contents(public_path('')) }}</style> --}}
    {{-- <link rel="stylesheet" href="http://backend/build/assets/app-C79Z4i2h.css"> --}}
    {{-- http://backend/ --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite('resources/css/app.css')
</head>
<body class="w-full min-w-[1280px] h-full p-1 bg-gray-800 font-roboto">
    @foreach ($passengers as $passenger)
        <div class="w-full h-fit p-6 flex flex-col gap-6">
            @foreach ($flights as $flight)
                <div class="mx-auto w-full 2xl:w-4/5 flex h-[26rem] rounded-xl overflow-hidden bg-transparent">
                    {{-- Ticket --}}
                    <div class="h-full w-auto grow relative text-gray-800">
                        <div class="w-full h-4/5 relative overflow-hidden flex items-start justify-center">
                            <img class="m-6 top-0 w-full h-auto opacity-10" src="{{ asset('images/ticket-world-map.svg') }}" alt="">
                            <div class="h-full w-full absolute bg-[#11B780]/70 top-0 left-0 flex flex-col justify-around">
                                <div class="flex justify-between items-center gap-4 mr-6 px-6 py-5">
                                    <div class="w-fit text-center">
                                        <a href="{{ $flight->departure_airport->google_maps_url }}" target="_blank" class="text-3xl font-bold">
                                            <abbr title="{{ $flight->departure_airport->name }}">{{ $flight->departure_airport->IATA_code }}</abbr>
                                        </a>
                                        <p class="text-sm mt-2">{{ $flight->departure_airport->name }}</p>
                                        {{-- <p class="text-sm mt-1 italic">{{ $flight->departure_airport->city_name }}, {{ $flight->departure_airport->country->name }}</p> --}}
                                    </div>
                                    <div class="w-auto grow flex justify-between items-center gap-4">
                                        <svg version="1.1" class="h-16 fill-gray-800" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 371.656 371.656" style="enable-background:new 0 0 371.656 371.656;" xml:space="preserve"><g><g><g><path d="M37.833,212.348c-0.01,0.006-0.021,0.01-0.032,0.017c-4.027,2.093-5.776,6.929-4.015,11.114 c1.766,4.199,6.465,6.33,10.787,4.892l121.85-40.541l-22.784,37.207c-1.655,2.703-1.305,6.178,0.856,8.497 c2.161,2.318,5.603,2.912,8.417,1.449l23.894-12.416c0.686-0.356,1.309-0.823,1.844-1.383l70.785-73.941l87.358-45.582 c33.085-17.835,29.252-31.545,27.29-35.321c-1.521-2.928-4.922-6.854-12.479-8.93c-7.665-2.106-18.021-1.938-31.653,0.514 c-4.551,0.818-7.063,0.749-9.723,0.676c-9.351-0.256-15.694,0.371-47.188,16.736L90.788,164.851l-66.8-34.668 c-2.519-1.307-5.516-1.306-8.035,0.004l-11.256,5.85c-2.317,1.204-3.972,3.383-4.51,5.938c-0.538,2.556,0.098,5.218,1.732,7.253 l46.364,57.749L37.833,212.348z"/><path d="M355.052,282.501H28.948c-9.17,0-16.604,7.436-16.604,16.604s7.434,16.604,16.604,16.604h326.104 c9.17,0,16.604-7.434,16.604-16.604C371.655,289.934,364.222,282.501,355.052,282.501z"/></g></g></g></svg>
                                        <div class="border-t-2 h-8 border-gray-800 border-dashed w-full grow flex items-center justify-center relative">
                                            <div class="absolute left-0 top-3 italic">
                                                {{ $flight->departs_at->format('M d, H:i') }}
                                            </div>
                                            <p class="absolute -top-8 flex items-center text-sm">
                                                <svg class="w-4 h-4 mx-1" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 55.668 55.668" style="enable-background:new 0 0 55.668 55.668;" xml:space="preserve"><g><g><path style="fill:#010002;" d="M27.833,0C12.487,0,0,12.486,0,27.834s12.487,27.834,27.833,27.834 c15.349,0,27.834-12.486,27.834-27.834S43.182,0,27.833,0z M27.833,51.957c-13.301,0-24.122-10.821-24.122-24.123 S14.533,3.711,27.833,3.711c13.303,0,24.123,10.821,24.123,24.123S41.137,51.957,27.833,51.957z"></path><path style="fill:#010002;" d="M41.618,25.819H29.689V10.046c0-1.025-0.831-1.856-1.855-1.856c-1.023,0-1.854,0.832-1.854,1.856 v19.483h15.638c1.024,0,1.855-0.83,1.854-1.855C43.472,26.65,42.64,25.819,41.618,25.819z"></path></g></g></svg>
                                               <strong>Duration</strong>&nbsp;{{ $flight->meta->journey->duration }}</p>
                                            @unless (is_null($flight->arrives_at))
                                                <div class="absolute right-0 top-3 italic">
                                                    {{ $flight->arrives_at->format('M d, H:i') }}
                                                </div>
                                            @endunless
                                        </div>
                                        <svg version="1.1" class="h-16 fill-gray-800" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 361.228 361.228" style="enable-background:new 0 0 361.228 361.228;" xml:space="preserve"><g><g><g><path d="M12.348,132.041c-0.012-0.001-0.023-0.003-0.036-0.005c-4.478-0.737-8.776,2.086-9.873,6.494 c-1.102,4.419,1.384,8.941,5.706,10.379l121.85,40.542l-40.533,16.141c-2.944,1.173-4.746,4.165-4.404,7.314 c0.34,3.151,2.741,5.688,5.87,6.203l26.57,4.373c0.763,0.125,1.541,0.125,2.304-0.002l100.975-16.795l97.254,15.842 c37.176,5.542,42.321-7.729,43.012-11.931c0.537-3.256,0.166-8.438-4.641-14.626c-4.875-6.279-13.269-12.348-25.652-18.553 c-4.135-2.072-6.104-3.632-8.188-5.284c-7.334-5.807-12.791-9.106-47.809-14.871L83.206,125.736L50.492,57.958 c-1.234-2.556-3.634-4.351-6.436-4.812l-12.517-2.061c-2.577-0.424-5.208,0.329-7.168,2.053 c-1.962,1.724-3.048,4.236-2.958,6.845l2.525,74.013L12.348,132.041z"/><path d="M342.707,277.051H16.604C7.434,277.051,0,284.484,0,293.654s7.434,16.604,16.604,16.604h326.103 c9.17,0,16.605-7.436,16.605-16.604S351.877,277.051,342.707,277.051z"/></g></g></g></svg>
                                    </div>
                                    <div class="w-fit text-center">
                                        <a href="{{ $flight->departure_airport->google_maps_url }}" target="_blank" class="text-3xl font-bold">
                                            <abbr title="{{ $flight->arrival_airport->name }}">{{ $flight->arrival_airport->IATA_code }}</abbr>
                                        </a>
                                        <p class="text-sm mt-2">{{ $flight->arrival_airport->name }}</p>
                                        {{-- <p class="text-sm mt-1 italic">{{ $flight->arrival_airport->city_name }}, {{ $flight->arrival_airport->country->name }}</p> --}}
                                    </div>
                                </div>
                                <div class="px-6 flex flex-col gap-2">
                                    <h1 class="text-2xl font-bold">Departure</h1>
                                    <div class="w-full pl-2 flex gap-4">
                                        <p><strong>Airport:</strong>&nbsp;{{ $flight->departure_airport->name }}, {{ $flight->departure_airport->city_name }}, {{ $flight->departure_airport->country->name }}</p>
                                        @unless (is_null($flight->departure_terminal))
                                            <p><strong>Terminal:</strong>&nbsp;{{ $flight->departure_terminal }}</p>
                                        @endunless
                                        <x-date :datetime="$flight->departs_at" />
                                        <x-time :datetime="$flight->departs_at" />
                                    </div>
                                </div>
                                <div class="px-6 flex flex-col gap-2">
                                    <h1 class="text-2xl font-bold">Arrival</h1>
                                    <div class="w-full pl-2 flex gap-4">
                                        <p><strong>Airport:</strong>&nbsp;{{ $flight->arrival_airport->name }}, {{ $flight->arrival_airport->city_name }}, {{ $flight->arrival_airport->country->name }}</p>
                                        @unless (is_null($flight->departure_terminal))
                                            <p><strong>Terminal:</strong>&nbsp;{{ $flight->departure_terminal }}</p>
                                        @endunless
                                        @unless (is_null($flight->arrives_at))
                                            <x-date :datetime="$flight->arrives_at" />
                                            <x-time :datetime="$flight->arrives_at" />
                                        @endunless
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full h-1/5 bg-white flex justify-around items-center gap-3 px-4">
                            <div class="flex gap-2 text-lg">
                                <h1 class="font-bold">Passenger</h1>
                                <p>{{ str($passenger->full_name)->upper($passenger->full_name) }}</p>
                            </div>
                            <div class="flex gap-2 text-lg">
                                <h1 class="font-bold">Passenger Type</h1>
                                <p>{{ ucfirst($passenger->gender) }}&comma;</p>
                                <p>{{ ucfirst($passenger->type) }}</p>
                            </div>
                            <div class="flex gap-2 text-lg">
                                <h1 class="font-bold">Flight</h1>
                                <p>{{ $flight->flight_number }}</p>
                            </div>
                            <div class="flex gap-2 text-lg">
                                <h1 class="font-bold">Cabin Type</h1>
                                <p>{{ $flight->meta->airplane->cabin_type }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-800 rounded-full w-14 h-14 absolute -top-7 -right-7"></div>
                        <div class="bg-gray-800 rounded-full w-14 h-14 absolute -bottom-7 -right-7"></div>
                    </div>
                    <div class="h-full w-1/4 border-l-[3px] bg-white text-gray-800 border-gray-600 border-dashed flex flex-col justify-between items-center py-2">
                        @if ($flight->marketing_airline)
                        <div class="ml-6 p-3 flex items-center justify-center gap-2">
                            <img class="w-10 h-10" width="48px" height="48px" src="{{ asset($flight->marketing_airline->logo) }}" alt="">
                            <h3 class="font-bold text-xl">{{ $flight->marketing_airline->name }}</h3>
                        </div>
                        @endif
                        <div class="px-3 flex flex-col gap-3">
                            <div class="flex gap-2 items-center w-full">
                                <svg class="w-5 h-5" viewBox="-42 0 512 512.002" xmlns="http://www.w3.org/2000/svg"><path d="m210.351562 246.632812c33.882813 0 63.222657-12.152343 87.195313-36.128906 23.972656-23.972656 36.125-53.304687 36.125-87.191406 0-33.875-12.152344-63.210938-36.128906-87.191406-23.976563-23.96875-53.3125-36.121094-87.191407-36.121094-33.886718 0-63.21875 12.152344-87.191406 36.125s-36.128906 53.308594-36.128906 87.1875c0 33.886719 12.15625 63.222656 36.132812 87.195312 23.976563 23.96875 53.3125 36.125 87.1875 36.125zm0 0"/><path d="m426.128906 393.703125c-.691406-9.976563-2.089844-20.859375-4.148437-32.351563-2.078125-11.578124-4.753907-22.523437-7.957031-32.527343-3.308594-10.339844-7.808594-20.550781-13.371094-30.335938-5.773438-10.15625-12.554688-19-20.164063-26.277343-7.957031-7.613282-17.699219-13.734376-28.964843-18.199219-11.226563-4.441407-23.667969-6.691407-36.976563-6.691407-5.226563 0-10.28125 2.144532-20.042969 8.5-6.007812 3.917969-13.035156 8.449219-20.878906 13.460938-6.707031 4.273438-15.792969 8.277344-27.015625 11.902344-10.949219 3.542968-22.066406 5.339844-33.039063 5.339844-10.972656 0-22.085937-1.796876-33.046874-5.339844-11.210938-3.621094-20.296876-7.625-26.996094-11.898438-7.769532-4.964844-14.800782-9.496094-20.898438-13.46875-9.75-6.355468-14.808594-8.5-20.035156-8.5-13.3125 0-25.75 2.253906-36.972656 6.699219-11.257813 4.457031-21.003906 10.578125-28.96875 18.199219-7.605469 7.28125-14.390625 16.121094-20.15625 26.273437-5.558594 9.785157-10.058594 19.992188-13.371094 30.339844-3.199219 10.003906-5.875 20.945313-7.953125 32.523437-2.058594 11.476563-3.457031 22.363282-4.148437 32.363282-.679688 9.796875-1.023438 19.964844-1.023438 30.234375 0 26.726562 8.496094 48.363281 25.25 64.320312 16.546875 15.746094 38.441406 23.734375 65.066406 23.734375h246.53125c26.625 0 48.511719-7.984375 65.0625-23.734375 16.757813-15.945312 25.253906-37.585937 25.253906-64.324219-.003906-10.316406-.351562-20.492187-1.035156-30.242187zm0 0"/></svg>
                                <strong>Passenger</strong>
                                <p class="bg-gray-200 grow w-auto px-2 py-1 italic rounded-md">{{ str($passenger->full_name)->upper() }}</p>
                            </div>
                            <div class="flex gap-2 items-center w-full">
                                <svg class="w-5 h-5" viewBox="-42 0 512 512.002" xmlns="http://www.w3.org/2000/svg"><path d="m210.351562 246.632812c33.882813 0 63.222657-12.152343 87.195313-36.128906 23.972656-23.972656 36.125-53.304687 36.125-87.191406 0-33.875-12.152344-63.210938-36.128906-87.191406-23.976563-23.96875-53.3125-36.121094-87.191407-36.121094-33.886718 0-63.21875 12.152344-87.191406 36.125s-36.128906 53.308594-36.128906 87.1875c0 33.886719 12.15625 63.222656 36.132812 87.195312 23.976563 23.96875 53.3125 36.125 87.1875 36.125zm0 0"/><path d="m426.128906 393.703125c-.691406-9.976563-2.089844-20.859375-4.148437-32.351563-2.078125-11.578124-4.753907-22.523437-7.957031-32.527343-3.308594-10.339844-7.808594-20.550781-13.371094-30.335938-5.773438-10.15625-12.554688-19-20.164063-26.277343-7.957031-7.613282-17.699219-13.734376-28.964843-18.199219-11.226563-4.441407-23.667969-6.691407-36.976563-6.691407-5.226563 0-10.28125 2.144532-20.042969 8.5-6.007812 3.917969-13.035156 8.449219-20.878906 13.460938-6.707031 4.273438-15.792969 8.277344-27.015625 11.902344-10.949219 3.542968-22.066406 5.339844-33.039063 5.339844-10.972656 0-22.085937-1.796876-33.046874-5.339844-11.210938-3.621094-20.296876-7.625-26.996094-11.898438-7.769532-4.964844-14.800782-9.496094-20.898438-13.46875-9.75-6.355468-14.808594-8.5-20.035156-8.5-13.3125 0-25.75 2.253906-36.972656 6.699219-11.257813 4.457031-21.003906 10.578125-28.96875 18.199219-7.605469 7.28125-14.390625 16.121094-20.15625 26.273437-5.558594 9.785157-10.058594 19.992188-13.371094 30.339844-3.199219 10.003906-5.875 20.945313-7.953125 32.523437-2.058594 11.476563-3.457031 22.363282-4.148437 32.363282-.679688 9.796875-1.023438 19.964844-1.023438 30.234375 0 26.726562 8.496094 48.363281 25.25 64.320312 16.546875 15.746094 38.441406 23.734375 65.066406 23.734375h246.53125c26.625 0 48.511719-7.984375 65.0625-23.734375 16.757813-15.945312 25.253906-37.585937 25.253906-64.324219-.003906-10.316406-.351562-20.492187-1.035156-30.242187zm0 0"/></svg>
                                <strong>Passenger Type</strong>
                                <p class="bg-gray-200 grow w-auto px-2 py-1 italic rounded-md">{{ ucfirst($passenger->gender) }}&comma;&nbsp;{{ ucfirst($passenger->type) }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 items-center w-full">
                                <svg class="w-5 h-5" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M256,0C161.896,0,85.333,76.563,85.333,170.667c0,28.25,7.063,56.26,20.49,81.104L246.667,506.5 c1.875,3.396,5.448,5.5,9.333,5.5s7.458-2.104,9.333-5.5l140.896-254.813c13.375-24.76,20.438-52.771,20.438-81.021 C426.667,76.563,350.104,0,256,0z M256,256c-47.052,0-85.333-38.281-85.333-85.333c0-47.052,38.281-85.333,85.333-85.333 s85.333,38.281,85.333,85.333C341.333,217.719,303.052,256,256,256z"/></g></g></svg>
                                <strong>From</strong>
                                <p class="bg-gray-200 grow w-auto px-2 py-1 italic rounded-md">
                                    <img class="w-5 h-5 mr-1 inline rounded-full" height="20px" width="20px" src="{{ asset('storage/images/flags/1x1/' . str($flight->departure_airport->country_code)->lower() . '.svg') }}" alt="">
                                    {{ $flight->departure_airport->city_name }}, {{ $flight->departure_airport->country->name }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 w-full">
                                <svg class="w-5 h-5" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M256,0C161.896,0,85.333,76.563,85.333,170.667c0,28.25,7.063,56.26,20.49,81.104L246.667,506.5 c1.875,3.396,5.448,5.5,9.333,5.5s7.458-2.104,9.333-5.5l140.896-254.813c13.375-24.76,20.438-52.771,20.438-81.021 C426.667,76.563,350.104,0,256,0z M256,256c-47.052,0-85.333-38.281-85.333-85.333c0-47.052,38.281-85.333,85.333-85.333 s85.333,38.281,85.333,85.333C341.333,217.719,303.052,256,256,256z"/></g></g></svg>
                                <strong>To</strong>
                                <p class="bg-gray-200 grow w-auto px-2 py-1 italic rounded-md">
                                    <img class="w-5 h-5 mr-1 inline rounded-full" height="20px" width="20px" src="{{ asset('storage/images/flags/1x1/' . str($flight->arrival_airport->country_code)->lower() . '.svg') }}" alt="">
                                    {{ $flight->arrival_airport->city_name }}, {{ $flight->arrival_airport->country->name }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-300 w-full flex justify-between items-center gap-2 p-2">
                            <p class="text-center text-xl">
                                <strong class="bg-[#173F28] inline-block text-gray-100 px-2">Flight</strong>
                                <br><span class="font-mono">{{ $flight->flight_number }}</span>
                            </p>
                            <p class="text-4xl font-bold">{{ $flight->departure_airport_code }}</p>
                            <svg class="w-12 h-12" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 510 510" style="enable-background:new 0 0 510 510;" xml:space="preserve"><g><g><path d="M510,255c0-20.4-17.85-38.25-38.25-38.25H331.5L204,12.75h-51l63.75,204H76.5l-38.25-51H0L25.5,255L0,344.25h38.25 l38.25-51h140.25l-63.75,204h51l127.5-204h140.25C492.15,293.25,510,275.4,510,255z"/></g></g></svg>
                            <p class="text-4xl font-bold">{{ $flight->arrival_airport_code }}</p>
                        </div>
                        <div class="w-full flex items-center justify-center">
                            <img class="h-16 w-auto" src="{{ asset('images/logo.png') }}" alt="">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>