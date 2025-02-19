@foreach ($passengers as $passenger)
    <div class="w-full h-fit p-6 flex flex-col gap-6">
        <x-ticket.notice :airline="name_fa($booking->airline)" :meta="$booking->meta" :notes="$booking->meta?->notes"/>
        @foreach ($passenger->tickets as $ticket)
            @php
                $flight = $flights[$loop->index]; 
            @endphp
            {{-- Ticket --}}
            <div class="mx-auto w-full 2xl:w-4/5 flex h-[29rem] rounded-xl overflow-hidden bg-transparent">
                {{-- Main - Left --}}
                @if (app()->isLocale('fa'))
                    @include('ticket.main-fa')
                @else
                    @include('ticket.main')
                @endif
                {{-- Side - Right --}}
                @include('ticket.side')
            </div>
        @endforeach
    </div>
@endforeach