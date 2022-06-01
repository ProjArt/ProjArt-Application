<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DC-Covers">
    <meta name="msapplication-TileImage" content="link to the image in static folder">
    <meta name="msapplication-TileColor" content="#000">

    <title>{{ config('app.name') }}</title>
    <base href="{{ env('MIX_BASE_URL') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }} " />
    <link rel="apple-touch-icon" href="link to the smaller icon">

    <link rel="stylesheet" href="{{ config('app.url') }}{{ mix('css/app.css') }}">



    {{-- PUSHER --}}
    @production
        <script src="{{ asset('js/pusher-prod.js') }}"></script>
    @else
        <script src="{{ asset('js/pusher.js') }}"></script>
    @endproduction

</head>

<body>
    <div id="app"></div>

    <script src="{{ config('app.url') }}{{ mix('js/app.js') }}"></script>
</body>

</html>
