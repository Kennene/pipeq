<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ url('/favicon.ico') }}">
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/user.js'])
</head>

<body class="flex flex-col h-screen">
    @include('topbar')

    <script>
        window.destinations = {!! json_encode($destinations->map(function ($destination) {
    return [
        'id' => $destination->id,
        'name' => __($destination->name),
        'description' => __($destination->description)
    ];
})) !!};

        window.token = @json($token);
        window.routes = {
            clear: "{{ route('_clear') }}"
        };
        window.statuses = {
            WAITING: {{ App\Models\Status::WAITING }},
            IN: {{ App\Models\Status::IN }},
            END: {{ App\Models\Status::END }}
        };

        window.translations = {
            "register.waiting.message": "{{ __('register.waiting.message') }}",
            "register.in.message": "{{ __('register.in.message') }}"
        };
    </script>
    <div id="app" class="flex-1 overflow-hidden"></div>
</body>

</html>