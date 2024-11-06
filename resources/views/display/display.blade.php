<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js']) 
    @vite(['resources/css/display.css']) 
    @pipeQColors 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Display</title>
</head>

<body>

    @include('topbar')

    @include('tickets-dashboard')
    

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
}

document.addEventListener('DOMContentLoaded', function() {
    PipeQ = new PipeQ();
});

</script>

</html>