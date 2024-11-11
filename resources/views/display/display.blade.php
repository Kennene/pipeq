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

    <div class="container-fluid">
        <div id="tickets-holder" class="d-flex flex-wrap justify-content-start"></div>
    </div>

</body>

<script>
const tickets = @json($tickets);

class PipeQ {
    constructor() {
        const channel = Echo.private(`display`);
        this.display = channel;

        // upon reload, for each existing ticket, create a card
        tickets.forEach(ticket => {
            this._updateTicket(ticket);
        });

        // listen for changes of tickets
        this._listenForChanges();
    }

    _listenForChanges() {
        this.display.listen('UpdateDisplayAboutTicket', (e) => {
            console.log('New update:', e);
            this._updateTicket(e.ticket);
        })
    }

    _updateTicket(ticket) {
        let ticket_card = document.getElementById(`ticket${ticket.id}`);

        // if ticket is closed, remove it
        if (ticket.status_id == 4) {
            // if card exists, remove it
            if (ticket_card) {
                ticket_card.remove();
            }
            return;
        }
        
        // if ticket card does not exist, create it
        if (!ticket_card) {
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

        // fill the card with ticket data
        ticket_card.querySelector('h5').textContent = ticket.user;
        ticket_card.querySelector('p').textContent = ticket.status;
        ticket_card.querySelector('h6').textContent = ticket.workstation;

        
        console.log(tickets);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    PipeQ = new PipeQ();
});

</script>

</html>