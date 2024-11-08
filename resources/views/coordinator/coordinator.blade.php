<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> <!-- Potrzebne do wysyłania zapytań POST -->
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
    <style>
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

    
    <!-- Przyciski pokazowe przesuwające i usuwające bilet -->
    <button class="btn btn-primary" onclick="PipeQ._move(1, 3);">Move ticket 1 to workstation 3 with status 2</button>
    <button class="btn btn-danger" onclick="PipeQ._end(10);">Usuń bilet z ticket_id 1</button>


    <!--
        Poniżej jest kod PHP, który wyświetla miejsca docelowe, oraz stanowiska 
        w tych miejscach docelowych. Analogicznie masz dostęp do $tickets i $statuses.
        Do wszystkich przekazywanych wartości możesz sobie podejrzeć kontroler.
        Jeżeli chcesz to mogę te dane Ci podać w inny sposób, np. w JSONie, to jest tylko
        jedna dodatkowa funkcja w PHP, więc nie ma problemu. Dzięki temu mógłbyś po swojej
        stronie użyć jakiejś funkcji JS'a typu JSON.parse() i masz gotowy obiekt.
        Tekst, który jest w __() jest automatycznie tłumaczony na wybrany język.
    -->
    <br/>
    @foreach($destinations as $destination)

        Miejsce docelowe o id {{ $destination->id }} i nazwie {{ __($destination->name) }}: <br/>
        
        @foreach($destination->workstations as $workstation)
            Posiada stanowisko {{ __($workstation->name) }} a id tego stanowiska to {{ $workstation->id }} <br/>
        @endforeach

    @endforeach


    <div id="app">
        <Coordinator></Coordinator>
    </div>

</body>


<script>

class PipeQ {
    constructor() {
        const channel = Echo.private(`display`);
        this.display = channel;
        this._updateDisplay();
    }

    _updateDisplay() {
        this.display.listen('UpdateDisplayAboutTicket', function(e) {
            @if(env('APP_ENV'))
                console.log(e);
            @endif

            let ticket = e.ticket;
            let ticket_card = document.getElementById(`ticket${ticket.id}`);

            // if ticket does nit exist, create it
            if(!ticket_card) {
                $('#tickets-holder').append(`
                    <div id="ticket${ticket.id}" value="${ticket.id}" class="ticket-card card m-3 shadow-lg">
                        <div class="card-body p-3">
                            <h5 class="h2 card-title text-center"></h5>
                            <hr>
                            <p class="card-text mt-2"></p>
                            <h6 class="card-subtitle mt-1"></h6>
                        </div>
                    </div>
                `);
                ticket_card = document.getElementById(`ticket${ticket.id}`);
            }

            // if workstation is not set, set it to destination
            if(!ticket.workstation) {
                ticket.workstation = ticket.destination;
            }

            ticket_card.querySelector('h5').textContent = ticket.user;
            ticket_card.querySelector('p').textContent = ticket.status;
            ticket_card.querySelector('h6').textContent = ticket.workstation;
        })
    }

    //* providing status id {!! App\Models\Status::END !!} has the same effect as _end method
    _move(ticket_id, workstation_id, status_id = {!! App\Models\Status::IN !!}) {
        @if(env('APP_DEBUG'))
            console.log(`Moving ticket ${ticket_id} to workstation ${workstation_id} with status ${status_id} using /move/${ticket_id}/${workstation_id}/${status_id}`);
        @endif

        axios.post(`/move/${ticket_id}/${workstation_id}/${status_id}`)
            .then(response => {
                @if(env('APP_DEBUG'))
                    console.log(response);
                @endif
            })
            .catch(error => {
                console.error(error.response.data.error);
            });
    }

    _end(ticket_id) {
        axios.post(`/end/${ticket_id}`)
            .then(response => {
                @if(env('APP_DEBUG'))
                    console.log(response);
                @endif

                let ticket_card = document.getElementById(`ticket${ticket_id}`);
                if(ticket_card) {
                    ticket_card.remove();
                } else {
                    console.error('Ticket card not found');
                }
            })
            .catch(error => {
                console.error(error.response.data.error);
            });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    PipeQ = new PipeQ();
});


</script>

</html>