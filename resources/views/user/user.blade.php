<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/user.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>

    @include('topbar')

    <div class="content">
        <div class="container mt-5">
            <div class="d-flex flex-column justify-content-center full-height">
                @foreach($destinations as $destination)
                    <button class="destination-button btn btn-primary shadow btn-lg btn-block py-9 mb-10 custom-font-size" onclick="pipeq._register({{ $destination->id }});" title="{{ __($destination->description) }}">
                        {{ __($destination->name) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>


    <!-- //! static style in here, remove it, if possible -->
    <!-- //* Waiting status page -->
    <div id="waitingPage" class="page">
        <div class="container-lg d-flex flex-column justify-content-start align-items-center" style="height: 100vh; padding-top: 5vh;">
            <!-- Ticket number -->
            <div id="waitingPage-ticket_nr" style="width: 20vh; height: 20vh; font-size: 10vh; background-color: pink;" class="d-flex justify-content-center align-items-center rounded-circle shadow-lg">00</div>

            <!-- Waiting message -->
            <br />
            <div class="user-message-container mt-4 p-3 rounded shadow-sm">
                <div class="user-message">
                    <p class="waiting-message">{{ __("register.waiting.message") }}</p>
                </div>
            </div>

            <!-- Waiting animation -->
            <br />
            <div id="waitingAnimation-container" class="mt-4">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div>


    <!-- //! static style in here, remove it, if possible -->
    <!-- //* In status page -->
    <div id="inPage" style="background-color: darkblue;" class="page">
        <div class="container-lg d-flex flex-column justify-content-start align-items-center" style="height: 100vh; padding-top: 5vh;">
            <!-- In animation -->
            <div class="inAnimation-container">
                <i class="bi bi-box-arrow-in-right bouncing"></i>
            </div>

            <!-- In message -->
            <div style="background-color: lightblue;" class="user-message-container p-3 rounded shadow-sm">
                <div class="user-message">
                    <p class="waiting-message">
                        <span id="inStaticText">{{ __("register.in.message") }}</span>
                        <span id="inWorkstationText">inWorkstationText</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    class PipeQ {
        constructor()
        {
            @if(!is_null($token))
                this._listen('{!! $token !!}');
            @endif
        }

        _listen(channel = null)
        {
            // if channel is passed, use it, otherwise use the one from the class
            if (channel != null) {
                this.channel = channel;
            }

            // Initialize Echo
            var register = Echo.channel(`register.${this.channel}`);

            @if(env('APP_DEBUG', false))
                console.log('Listening on channel', register.name);
            @endif


            // Listen for ticket update and decide what to do
            register.listen('UpdateUserAboutHisTicket', (message) => {
                @if(env('APP_DEBUG', false))
                    console.log('Event UpdateUserAboutHisTicket:', message);
                @endif

                var ticket = message.ticket;
                $('.page').removeClass('show');

                switch (ticket.status_id) {
                    case {{ App\Models\Status::WAITING }}:
                        displayStatusWaiting(ticket);
                        break;

                    case {{ App\Models\Status::IN }}:
                        displayStatusIn(ticket);
                        break;

                    case {{ App\Models\Status::END }}:
                        this._clear();
                        displayStatusEnd(ticket);
                        break;

                    default:
                        console.error('Unknown status', ticket.status_id);
                        break;
                }
            });

            // when the WebSocket channel is ready, request update on ticket
            register.subscribed(() => {
                axios.post(`/status/${this.channel}`);
            });
        }

        _register(destination_id)
        {
            axios.post(`/register/${destination_id}`)
                .then(response => {
                    @if(env('APP_DEBUG', false))
                        console.log('Register response:', response.data);
                    @endif

                    // Get channel from response (if it exists)
                    if( response.data.channel) {
                        this.channel = response.data.channel;
                        this._listen(this.channel);
                    } else {
                        console.error('Something went wrong! Channel not received from HTTP request');
                    }
                })
                .catch(error => {
                    // if error message is handled by the server, display it, otherwise display generic error
                    if (error.response.data.error) {
                        console.error(error.response.data.error);
                    } else {
                        console.error(error);
                    }
                });
        }

        _clear()
        {
            axios.post('{!! route("_clear"); !!}')
                .then(response => {
                    console.log(response.data.message);
                })
                .catch(error => {
                    console.log(error);
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        pipeq = new PipeQ();
    });

    function displayStatusWaiting(ticket) {
        $('#waitingPage-ticket_nr').html(ticket.ticket_nr);
        $('#waitingPage').addClass('show');

        // Show waiting animation after some time
        setTimeout(function() {
            document.getElementById("waitingAnimation-container").style.opacity = "1";
        }, 2000);
    }

    function displayStatusIn(ticket) {
        $('#inPage').addClass('show');
        $('#inWorkstationText').text(ticket.workstation.toLowerCase());
    }

    function displayStatusEnd(ticket) {
        console.error('unimplemented');
        console.log('Dziękujemy za skorzystanie z naszych usług');
    }

</script>

</html>