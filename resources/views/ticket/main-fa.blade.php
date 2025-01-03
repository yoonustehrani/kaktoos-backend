<div class="h-full w-auto grow relative text-gray-800">
    {{-- Top --}}
    <div class="w-full h-4/5 relative overflow-hidden flex items-start justify-center">
        <img class="m-6 top-0 w-full h-auto opacity-10" src="{{ asset('images/ticket-world-map.svg') }}" alt="">
        {{-- Inside --}}
        <div class="h-full w-full absolute bg-[#11B780]/70 top-0 left-0 flex flex-col justify-around">
            {{-- Top --}}
            <div class="flex justify-between items-center gap-4 mr-6 px-6 py-5">
                <div class="w-fit text-center">
                    <a href="{{ $flight->departure_airport->google_maps_url }}" target="_blank" class="text-3xl font-bold">
                        <abbr title="{{ $flight->departure_airport->name }}">{{ $flight->departure_airport->IATA_code }}</abbr>
                    </a>
                    <p class="text-sm mt-2">{{ $flight->departure_airport->name }}</p>
                    {{-- <p class="text-sm mt-1 italic">{{ $flight->departure_airport->city_name }}, {{ $flight->departure_airport->country->name }}</p> --}}
                </div>
                <div class="w-auto grow flex justify-between items-center gap-4">
                    <svg version="1.1" class="h-16 fill-gray-800 scale-x-[-1]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 371.656 371.656" style="enable-background:new 0 0 371.656 371.656;" xml:space="preserve"><g><g><g><path d="M37.833,212.348c-0.01,0.006-0.021,0.01-0.032,0.017c-4.027,2.093-5.776,6.929-4.015,11.114 c1.766,4.199,6.465,6.33,10.787,4.892l121.85-40.541l-22.784,37.207c-1.655,2.703-1.305,6.178,0.856,8.497 c2.161,2.318,5.603,2.912,8.417,1.449l23.894-12.416c0.686-0.356,1.309-0.823,1.844-1.383l70.785-73.941l87.358-45.582 c33.085-17.835,29.252-31.545,27.29-35.321c-1.521-2.928-4.922-6.854-12.479-8.93c-7.665-2.106-18.021-1.938-31.653,0.514 c-4.551,0.818-7.063,0.749-9.723,0.676c-9.351-0.256-15.694,0.371-47.188,16.736L90.788,164.851l-66.8-34.668 c-2.519-1.307-5.516-1.306-8.035,0.004l-11.256,5.85c-2.317,1.204-3.972,3.383-4.51,5.938c-0.538,2.556,0.098,5.218,1.732,7.253 l46.364,57.749L37.833,212.348z"/><path d="M355.052,282.501H28.948c-9.17,0-16.604,7.436-16.604,16.604s7.434,16.604,16.604,16.604h326.104 c9.17,0,16.604-7.434,16.604-16.604C371.655,289.934,364.222,282.501,355.052,282.501z"/></g></g></g></svg>
                    <div class="border-t-2 h-8 border-gray-800 border-dashed w-full grow flex flex-row-reverse items-center justify-center relative">
                        <div class="absolute right-0 top-3 italic">
                            {{ jalali($flight->departs_at)->format('%d %B، H:i') }}
                        </div>
                        <p class="absolute -top-8 flex items-center text-sm">
                            <svg class="w-4 h-4 mx-1" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 55.668 55.668" style="enable-background:new 0 0 55.668 55.668;" xml:space="preserve"><g><g><path style="fill:#010002;" d="M27.833,0C12.487,0,0,12.486,0,27.834s12.487,27.834,27.833,27.834 c15.349,0,27.834-12.486,27.834-27.834S43.182,0,27.833,0z M27.833,51.957c-13.301,0-24.122-10.821-24.122-24.123 S14.533,3.711,27.833,3.711c13.303,0,24.123,10.821,24.123,24.123S41.137,51.957,27.833,51.957z"></path><path style="fill:#010002;" d="M41.618,25.819H29.689V10.046c0-1.025-0.831-1.856-1.855-1.856c-1.023,0-1.854,0.832-1.854,1.856 v19.483h15.638c1.024,0,1.855-0.83,1.854-1.855C43.472,26.65,42.64,25.819,41.618,25.819z"></path></g></g></svg>
                           <strong>مدت زمان پرواز</strong>&nbsp;{{ $flight->meta->journey->duration }}
                        </p>
                        @unless (is_null($flight->arrives_at))
                            <div class="absolute left-0 top-3 italic">
                                {{ jalali($flight->arrives_at)->format('%d %B، H:i') }}
                            </div>
                        @endunless
                    </div>
                    <svg version="1.1" class="h-16 fill-gray-800 scale-x-[-1]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 361.228 361.228" style="enable-background:new 0 0 361.228 361.228;" xml:space="preserve"><g><g><g><path d="M12.348,132.041c-0.012-0.001-0.023-0.003-0.036-0.005c-4.478-0.737-8.776,2.086-9.873,6.494 c-1.102,4.419,1.384,8.941,5.706,10.379l121.85,40.542l-40.533,16.141c-2.944,1.173-4.746,4.165-4.404,7.314 c0.34,3.151,2.741,5.688,5.87,6.203l26.57,4.373c0.763,0.125,1.541,0.125,2.304-0.002l100.975-16.795l97.254,15.842 c37.176,5.542,42.321-7.729,43.012-11.931c0.537-3.256,0.166-8.438-4.641-14.626c-4.875-6.279-13.269-12.348-25.652-18.553 c-4.135-2.072-6.104-3.632-8.188-5.284c-7.334-5.807-12.791-9.106-47.809-14.871L83.206,125.736L50.492,57.958 c-1.234-2.556-3.634-4.351-6.436-4.812l-12.517-2.061c-2.577-0.424-5.208,0.329-7.168,2.053 c-1.962,1.724-3.048,4.236-2.958,6.845l2.525,74.013L12.348,132.041z"/><path d="M342.707,277.051H16.604C7.434,277.051,0,284.484,0,293.654s7.434,16.604,16.604,16.604h326.103 c9.17,0,16.605-7.436,16.605-16.604S351.877,277.051,342.707,277.051z"/></g></g></g></svg>
                </div>
                <div class="w-fit text-center">
                    <a href="{{ $flight->departure_airport->google_maps_url }}" target="_blank" class="text-3xl font-bold">
                        <abbr title="{{ $flight->arrival_airport->name }}">{{ $flight->arrival_airport->IATA_code }}</abbr>
                    </a>
                    <p class="text-sm mt-2">{{ $flight->arrival_airport->name }}</p>
                    {{-- <p class="text-sm mt-1 italic">{{ $flight->arrival_airport->city_name }}, {{ $flight->arrival_airport->country->name }}</p> --}}
                </div>
            </div>
            {{-- <div class="px-6 flex flex-col gap-2 text-lg">
                <h1 class="text-2xl font-bold">@lang('Passenger')</h1>
                <p>{{ str($passenger->full_name)->upper($passenger->full_name) }}</p>
            </div> --}}
            {{-- Middle --}}
            <div class="px-6 flex flex-col gap-2">
                <h1 class="text-2xl font-bold">@lang('Departure')</h1>
                <div class="w-full pl-2 flex gap-4">
                    <p>
                        <strong>@lang('Airport'):</strong>
                        {{ name_fa($flight->departure_airport) }}@lang(',')
                        {{ name_fa($flight->departure_airport, 'city_name') }}@lang(',') {{ name_fa($flight->departure_airport->country) }}
                    </p>
                    @unless (is_null($flight->departure_terminal))
                        <p><strong>Terminal:</strong>&nbsp;{{ $flight->departure_terminal }}</p>
                    @endunless
                    <x-date lang="fa" :datetime="$flight->departs_at" />
                    <x-time :datetime="$flight->departs_at" />
                </div>
            </div>
            <div class="px-6 flex flex-col gap-2">
                <h1 class="text-2xl font-bold">@lang('Arrival')</h1>
                <div class="w-full pl-2 flex gap-4">
                    <p>
                        <strong>@lang('Airport'):</strong>
                        {{ name_fa($flight->arrival_airport) }}@lang(',')
                        {{ name_fa($flight->arrival_airport, 'city_name') }}@lang(',') {{ name_fa($flight->arrival_airport->country) }}
                    </p>
                    @unless (is_null($flight->departure_terminal))
                        <p><strong>@lang('Terminal'):</strong>&nbsp;{{ $flight->departure_terminal }}</p>
                    @endunless
                    @unless (is_null($flight->arrives_at))
                        <x-date lang="fa" :datetime="$flight->arrives_at" />
                        <x-time :datetime="$flight->arrives_at" />
                    @endunless
                </div>
            </div>
        </div>
    </div>
    @include('ticket.bottom')
    <div class="bg-gray-800 rounded-full w-14 h-14 absolute -top-7 -left-7"></div>
    <div class="bg-gray-800 rounded-full w-14 h-14 absolute -bottom-7 -left-7"></div>
</div>