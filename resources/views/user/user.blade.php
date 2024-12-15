<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/user.js'])
</head>

<body>
    <script>
        console.log(@json($destinations));
        window.destinations = @json($destinations);
        window.token = @json($token);
        window.routes = {
            clear: "{{ route('_clear') }}"
        };
        window.statuses = {
            WAITING: {{ App\Models\Status::WAITING }},
            IN: {{ App\Models\Status::IN }},
            END: {{ App\Models\Status::END }}
        };
    </script>
    <div id="app">
    </div>
</body>

</html>