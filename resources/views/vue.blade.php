<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <base href="{{ env('MIX_BASE_URL') }}">
    <link rel="stylesheet" href="{{ config('app.url') }}{{ mix('css/app.css') }}">

    {{-- ONE SIGNAL --}}
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "5786d114-d551-43c0-9705-d79e26fb9461",
            });
        });
    </script>
</head>

<body>
    <div id="app"></div>

    <script src="{{ config('app.url') }}{{ mix('js/app.js') }}"></script>
</body>

</html>
