<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    @vite(['resources/js/user.js'])
    <title>{{ env('APP_NAME', 'PipeQ') }}</title>
</head>

<body class="flex flex-col h-screen">
    @include('topbar')


    @foreach($destinations as $destination)
        <div class="bg-info text-xl">Jeżeli wybrano destination_id {{ $destination->id }}, czyli {{ $destination->name }} to pojawia się:</div>

        @foreach($destination->reasons as $reason)
            <a
                class="mt-1 text-secondary text-muted fst-italic border border-secondary rounded bg-light text-center py-1 shadow-sm"
                style="width: 30%; cursor: pointer; transition: transform 0.2s ease-in-out;"
                onmouseover="this.style.transform='scale(1.05)';"
                onmouseout="this.style.transform='scale(1)';"
                href="{{ route('_updateReason', [$reason, $token]) }}"
            >
                reason_id {{ $reason->id }}: {{ $reason->description }}
            </a>
        @endforeach

        <div
            class="mt-1 text-secondary text-muted fst-italic border border-secondary rounded bg-light text-center py-1 shadow-sm"
            style="width: 30%; cursor: pointer; transition: transform 0.2s ease-in-out;"
            onmouseover="this.style.transform='scale(1.05)';"
            onmouseout="this.style.transform='scale(1)';"
        >
            Fakowy reason, który nic nie wysyła, tylko zamyka menu: {{ __("reason.other") }}
        </div>

    @endforeach
    
    <div class="bg-warning text-xl text-black">PO WYBRANIU REASON, MENU WYBORU ZNIKA</div>
    <div class="bg-secondary text-l text-white">Z racji, że to nie jest obowiązkowe menu, to lepiej, żeby wyglądało drugorzędnie, opcjonalnie, mniej więcej jak powyżej</div>



    <script>
        window.destinations = @json($destinations->toArray());

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