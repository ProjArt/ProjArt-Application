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

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }} " />
    <link rel="apple-touch-icon" href="link to the smaller icon">

    {{-- PUSHER --}}
    {{-- <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script> --}}

    {{-- ICONS --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
        }
    </style>
</head>

<body>
    <div id="app"></div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>

<style>
    #app {
        position: absolute;
        width: 100vw;
        left: 0px;
    }
</style>
