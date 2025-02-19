@php
    if (
        request()->query('lang') == 'fa'
        // ||
        // ($booking->origin_airport->country_code  == 'IR' && $booking->destination_airport->country_code == 'IR')
    ) {
        App::setLocale('fa');
    } else {
        App::setLocale('en');
    }
    
@endphp
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
<body class="w-full min-w-[1280px] h-full p-1 bg-gray-800 {{ app()->isLocale('fa') ? 'font-vazir' : 'font-roboto' }}" dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}">
    @include('ticket.body')
</body>
</html>