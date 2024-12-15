<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/coordinator.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator</title>
</head>

<body class="flex flex-col h-screen">
    <script>
        window.STATUS_IN = {{ \App\Models\Status::IN }};
        window.STATUS_END = {{ \App\Models\Status::END }};
    </script>

    <!-- Topbar -->
    @include('topbar')

    <!-- Główny kontener -->
    <div id="app" class="flex-1 overflow-hidden">
        <!-- Komponent Coordinator -->
        <coordinator :initial-tickets='@json($tickets)' :translations='@json($translations)'
            :destinations='@json($destinations)'>
        </coordinator>
    </div>
</body>

</html>