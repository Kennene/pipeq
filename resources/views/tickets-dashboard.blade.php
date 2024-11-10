<!-- Resource no longer needed by display blade, but used foor coordinator// todo: delete this -->

@vite(['resources/css/tickets-dashboard.css'])

<div class="container-fluid">
    <div id="tickets-holder" class="d-flex flex-wrap justify-content-start">

        @foreach($tickets as $ticket)
            <div id="ticket{!! $ticket->id !!}" value="{!! $ticket->id !!}" class="ticket-card card m-3 shadow-lg">
                <div class="card-body p-3">
                    <h5 class="h2 card-title text-center">{{ __($ticket->user) }}</h5>
                    <hr>
                    <p class="card-text mt-2">{{ __($ticket->status) }}</p>
                    <h6 class="card-subtitle mt-1">{{ __($ticket->destination) }}</h6>
                </div>
            </div>
        @endforeach
        
    </div>
</div>