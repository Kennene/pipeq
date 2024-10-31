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

<style>
#tickets-display {
    max-height: 400px;
    overflow: scroll;
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<body>

    @include('topbar')

    <div id="tickets-display">
        @include('tickets-dashboard')
    </div>

    <button class="btn btn-secondary" onclick="PipeQ._move(3, 2, 4);">Move ticket 3 to workstation 2 with status 4</button>


    <div id="app">
        <Coordinator></Coordinator>
    </div>

</body>


<script>

class PipeQ {
    constructor() {
        const channel = Echo.private(`display`);
        this.display = channel;
        this._listen();
    }

    _listen() {
        this.display.listen('TicketNew', function(e) {
            @if(env('APP_ENV'))
                console.log(e);
            @endif

            let ticket = e.message;
            let ticket_card = document.getElementById(`ticket${ticket.id}`);

            if(ticket_card) {
                console.error('Ticket already exists');
                return;
            }

            $('#tickets-holder').append(`
                <div id="ticket${ticket.id}" class="card m-3 shadow-lg bg-dark text-light" style="width: 21rem;">
                    <div class="card-body p-3">
                        <h5 class="card-title text-center" style="font-size: 2.4rem; font-weight: bold; margin-bottom: 0.8rem;">${ticket.user}</h5>
                        <hr>
                        <p class="card-text mt-2" style="font-size: 1.7rem; font-weight: bold;">${ticket.status}</p>
                        <h6 class="card-subtitle mt-1" style="font-size: 1rem;">${ticket.destination}</h6>
                    </div>
                </div>
            `);
        })
    }

    _move(ticket_id, workstation_id, status_id = 2) {
        axios.post(`/move/${ticket_id}/${workstation_id}`, {
            status_id: status_id
        })
        .then(response => {
            @if(env('APP_DEBUG'))
                console.log(response);
            @endif
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