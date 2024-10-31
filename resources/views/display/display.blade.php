<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/display.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display</title>
</head>

<style>
    body {
        background-color: lightgreen;
    }
</style>

<body>

    @include('topbar')

    
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-start">

            @foreach($tickets as $ticket)
                <div id="ticket{!! $ticket->id !!}" class="card m-3 shadow-lg bg-dark text-light" style="width: 21rem;">
                    <div class="card-body p-3">
                        <h5 class="card-title text-center" style="font-size: 2.4rem; font-weight: bold; margin-bottom: 0.8rem;">{{ __($ticket->user) }}</h5>
                        <hr>
                        <p class="card-text mt-2" style="font-size: 1.7rem; font-weight: bold;">{{ __($ticket->status) }}</p>
                        <h6 class="card-subtitle mt-1" style="font-size: 1rem;">{{ __($ticket->destination) }}</h6>
                    </div>
                </div>
            @endforeach
            
        </div>
    </div>

</body>

<script>

class PipeQ {
    constructor() {
        const channel = Echo.private(`display`);
        this.display = channel;
        this._update();
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
                    ticket_card.querySelector('.card-text').textContent = ticket.status;
                    ticket_card.querySelector('.card-subtitle').textContent = ticket.workstation;
                    break;
                case 2:
                    ticket_card.querySelector('.card-text').textContent = ticket.status;
                    ticket_card.querySelector('.card-subtitle').textContent = ticket.workstation;
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