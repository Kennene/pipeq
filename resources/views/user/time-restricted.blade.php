<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Not available</title>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @pipeQColors
</head>

<style>
    .container {
        max-width: 40%;
    }
    
    .card {
        border-radius: 10px;
    }

    #time-restricted-title {
        font-weight: bold;
        color: var(--accent2);
    }

    #time-restricted-message-soonest {
        font-weight: bold;
    }

    #time-restricted-sorry-link {
        text-decoration: underline;
        color: var(--primary);
    }
</style>

<body>

    @include('topbar')

    <div class="container text-center mt-12">
        <div class="card p-4 shadow">
            <span id="time-restricted-title" class="h1">{{ __("time-restricted.title") }}</span><hr>
            <p id="time-restricted-message" class="mt-4">
                {{ __("time-restricted.message") }}
                <span id="time-restricted-message-soonest">{{ $opens ?? "" }}.</span>
            </p>
            <p id="time-restricted-sorry" class="mt-4">
                {{ __("time-restricted.sorry") }}
                <a href="https://portal.wsb.pl/group/poznan/kontakt-z-dzialami?id=299" id="time-restricted-sorry-link">portal.wsb.pl/group/poznan/kontakt-z-dzialami?id=299</a>
            </p>
            <p id="time-restricted-contact" class="mt-4">
                {{ __("time-restricted.contact") }}
                <ul class="list-group list-group-flush text-start mx-auto mt-2">
                    <li class="list-group-item">📧 <a href="mailto:dziekanat@poznan.merito.pl">dziekanat@poznan.merito.pl</a></li>
                    <!-- <li class="list-group-item">📞 <a href="tel:61 655 33 15">61 655 33 15</a></li> -->
                </ul>
            </p>
            <p id="time-restricted-footnote" class="mt-4">
                {!! __("time-restricted.footnote") !!}
            </p>
        </div>
    </div>


</body>


</html>