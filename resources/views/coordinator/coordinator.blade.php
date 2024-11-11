<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/coordinator.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator</title>
</head>

<body>

    @include('topbar')

    <script>
        window.STATUS_IN = {{ \App\Models\Status::IN }};
        window.STATUS_END = {{ \App\Models\Status::END }};
        console.log(@json($tickets));
    </script>

    <div id="app">
        <coordinator :initial-tickets='@json($tickets)' :translations='@json($translations)'></coordinator>
    </div>

</body>

</html>