<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/coordinator.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Coordinator</title>
</head>

<body>

    @include('topbar')

    <!--
    Mateusz, poniżej wrzucam Ci pokazowy kod, jak aplikacja działa :)
    Możesz ten kod usunąć, on jest tylko w celu demonstracyjnym, żebyś wiedział
    jak zaimplementować te funkcje po swojej stronie
-->

    <!--
    Jeżeli nie chcesz używać tego widoku to spoko, zrobimy osobny.
    Preferuję, żeby osoby obsługujące bilety widziały dokładnie ten sam
    ekran co osoby czekające, ale jeżeli nie da rady tego zintegrować
    to wymyślimy coś innego
-->
    <!-- <style>
        #tickets-display {
            max-height: 400px;
            overflow: scroll;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    
    <div id="tickets-display">
        @include('tickets-dashboard')
    </div>


    <!-- Przyciski pokazowe przesuwające i usuwające bilet 
    <button class="btn btn-primary" onclick="PipeQ._move(1, 3);">Move ticket 1 to workstation 3 with status 2</button>
    <button class="btn btn-danger" onclick="PipeQ._end(10);">Usuń bilet z ticket_id 1</button> -->


    <!--
    Poniżej jest kod PHP, który wyświetla miejsca docelowe, oraz stanowiska 
    w tych miejscach docelowych. Analogicznie masz dostęp do $tickets i $statuses.
    Do wszystkich przekazywanych wartości możesz sobie podejrzeć kontroler.
    Jeżeli chcesz to mogę te dane Ci podać w inny sposób, np. w JSONie, to jest tylko
    jedna dodatkowa funkcja w PHP, więc nie ma problemu. Dzięki temu mógłbyś po swojej
    stronie użyć jakiejś funkcji JS'a typu JSON.parse() i masz gotowy obiekt.
    Tekst, który jest w __() jest automatycznie tłumaczony na wybrany język.

    <br />
    @foreach($destinations as $destination)

        Miejsce docelowe o id {{ $destination->id }} i nazwie {{ __($destination->name) }}: <br />

        @foreach($destination->workstations as $workstation)
            Posiada stanowisko {{ __($workstation->name) }} a id tego stanowiska to {{ $workstation->id }} <br />
        @endforeach

    @endforeach -->

    <script>
        window.STATUS_IN = {{ \App\Models\Status::IN }};
        window.STATUS_END = {{ \App\Models\Status::END }};
        console.log(@json($tickets));
    </script>


    <div id="app">
        <coordinator :initial-tickets='@json($tickets)' :translations='@json($translations)'></coordinator>
    </div>


</body>

@push('scripts')
    <script src="{{ mix('js/coordinator.js') }}"></script>
@endpush

</html>