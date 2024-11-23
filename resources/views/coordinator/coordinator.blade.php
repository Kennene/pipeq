<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/coordinator.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator</title>
</head>

<body>


    <script>
        window.STATUS_IN = {{ \App\Models\Status::IN }};
        window.STATUS_END = {{ \App\Models\Status::END }};
        console.log(@json($tickets));
        console.log(@json($destinations));
    </script>
    @include('topbar')
    <div id="app">
        <coordinator :initial-tickets='@json($tickets)' :translations='@json($translations)'
            :destinations='@json($destinations)'>
        </coordinator>
    </div>

</body>

</html>