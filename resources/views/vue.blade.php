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

    <link rel="manifest" href="{{ asset('manifest.json') }} " />
    <link rel="apple-touch-icon" href="link to the smaller icon">

    {{-- PUSHER --}}
    <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>

    {{-- ICONS --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="/css/app.css">

</head>

<body>
    <div id="app"></div>
    <script src="/js/app.js"></script>
</body>

</html>
