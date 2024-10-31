<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/display.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Display</title>
</head>

<style>
    body {
        background-color: lightgreen;
    }
</style>

<body>

    @include('topbar')

    @include('tickets-dashboard')
    

</body>

<script>

class PipeQ {
    constructor() {
        const channel = Echo.private(`display`);
        this.display = channel;
        this._update();
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

    _update() {
        this.display.listen('TicketUpdate', function(e) {
            @if(env('APP_ENV'))
                console.log(e);
            @endif

            let ticket = e.message;
            let ticket_card = document.getElementById(`ticket${ticket.id}`);

            switch(ticket.status_id) {
                case 1:
                case 2:
                    $(ticket_card).find('.card-text').text(ticket.status);
                    $(ticket_card).find('.card-subtitle').text(ticket.workstation);
                    break;
                case 3:
                    console.log('Not implemented yet');
                    break;
                case 4:
                    if(ticket_card) {
                        ticket_card.remove();
                    } else {
                        console.error('Ticket not found');
                    }
                    break;
            }
        })
    }
}

document.addEventListener('DOMContentLoaded', function() {
    PipeQ = new PipeQ();
});

</script>

</html>