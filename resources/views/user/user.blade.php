<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    @vite(['resources/js/user.js'])
    <title>{{ env('APP_NAME', 'PipeQ') }}</title>
</head>

<body class="flex flex-col h-screen">
    @include('topbar')

    <script>
        window.destinations = @json($opened_destinations->toArray());

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
            "register.waiting.message": "{!! __('register.waiting.message') !!}",
            "register.in.message": "{!! __('register.in.message') !!}",
            "register.reason.message": "{!! __('register.reason.message') !!}",
            "register.reason.choose": "{!! __('register.reason.choose') !!}",
            "reason.other": "{!! __('reason.other') !!}",
            "register.end.button": "{!! __('register.end.button') !!}",
            "register.end.modal.title": "{!! __('register.end.modal.title') !!}",
            "register.end.modal.description": "{!! __('register.end.modal.description') !!}",
            "register.end.modal.confirm": "{!! __('register.end.modal.confirm') !!}",
            "register.end.modal.cancel": "{!! __('register.end.modal.cancel') !!}"
        };
        window.reasons = @json($reasons->toArray());
    </script>
    <div id="app" class="flex-1 overflow-hidden">
    </div>
</body>

</html>