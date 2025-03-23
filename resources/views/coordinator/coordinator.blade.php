<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    @vite(['resources/css/coordinator.css', 'resources/js/coordinator.js'])
    <title>Coordinator</title>
</head>

<body class="flex flex-col h-screen">
    <script>
        window.STATUS_IN = {{ \App\Models\Status::IN }};
        window.STATUS_END = {{ \App\Models\Status::END }};
        window.IS_COORDINATOR_SOUND = '{!! config("pipeq.is_coordinator_sound", false) !!}';
        window.NOTIFICATION_SOUND_PATH = '{!! config("pipeq.notification_sound_path") !!}';
    </script>

    <!-- Topbar -->
    @include('topbar')

    <!-- Główny kontener -->
    <div id="app" class="flex-1 overflow-hidden">
        <!-- Komponent Coordinator -->
        <coordinator
            :initial-tickets='@json($tickets)'
            :translations='@json($translations)'
            :destinations='@json($destinations)'
            :locales='@json(config('app.available_locales'))'
        >
        </coordinator>
    </div>
</body>

</html>