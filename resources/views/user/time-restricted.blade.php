<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    <title>Not available</title>
</head>

<style>    
    .card {
        border-radius: 10px;
    }

    #time-restricted-title {
        font-weight: bold;
        color: var(--red);
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