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
                <div class="card m-3 shadow-lg bg-dark text-light" style="width: 20rem;">
                    <div class="card-body p-3">
                        <h5 class="card-title text-center" style="font-size: 2.4rem; font-weight: bold; margin-bottom: 0.8rem;">{{ __($ticket->user) }}</h5>
                        <hr>
                        <p class="card-text mt-2" style="font-size: 1.7rem; font-weight: bold;">{{ __($ticket->status) }}</p>
                        <h6 class="card-subtitle text-muted mt-1" style="font-size: 1rem;">{{ __($ticket->destination) }}</h6>
                    </div>
                </div>
            @endforeach
            
        </div>
    </div>

    





    

</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // todo: po połączeniu display poproś o wszystkie otwarte tickety

        const channel = Echo.private('display');

        // todo: handle event TicketMove
        channel.listen('TicketMove', function(e) {
            console.log(e);
        })
    });
</script>

</html>