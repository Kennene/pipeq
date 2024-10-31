<div class="container-fluid">
    <div id="tickets-holder" class="d-flex flex-wrap justify-content-start">

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