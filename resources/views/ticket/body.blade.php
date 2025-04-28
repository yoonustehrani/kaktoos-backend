@foreach ($passengers as $passenger)
    <div class="w-full h-fit p-6 flex flex-col gap-6">
        <x-ticket.notice :airline="name_fa($booking->airline)" :meta="$booking->meta" :notes="$booking->meta?->notes"/>
        @foreach ($flights as $flight)
            {{-- $passenger->tickets as $ticket --}}
            @php
                $ticket = $passenger->tickets[$loop->index] ?? $passenger->tickets->first(); 
            @endphp
            {{-- Ticket --}}
            <div class="mx-auto w-full flex h-[29rem] rounded-xl overflow-hidden bg-transparent">
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