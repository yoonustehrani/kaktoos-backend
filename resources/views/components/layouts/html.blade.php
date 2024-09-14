<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-screen w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? '' }}</title>
    {{ $head ?? '' }}
</head>
<body class="w-full h-full">
    {{ $slot }}
</body>
</html>