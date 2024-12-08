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

<style>
    .destination-button {
        height: 30vh;
        font-size: 3.5rem;
    }

    .btn-animated {
        transition:
            background-color 0.3s ease,
            transform 0.2s ease;
    }

    .btn-animated:active {
        transform: scale(0.9);
    }



    #inPage {
        background-color: #0d6efd;

    }

    .page {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgb(255, 255, 255);
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateY(100%);
        opacity: 0;
        transition:
            transform 0.5s ease,
            opacity 0.5s ease;
        visibility: hidden;
        top: 80px;
    }

    .page.show {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .loader {
        border: 8px solid #0d6efd;
        border-top: 8px solid #ffffff;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        animation: spin 1s linear infinite;
    }

    .loading-container {
        text-align: center;
        color: #000000;
    }

    .loading-container p {
        font-size: 24px;
        color: white;
        background-color: #0d6efd;
        border-radius: 8px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .dot {
        width: 25px;
        height: 25px;
        margin: 0 15px;
        background-color: #0d6efd;
        border-radius: 50%;
        animation: dot-blink 1.5s infinite;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .dot:nth-child(1) {
        animation-delay: 0s;
    }

    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    .dot:nth-child(4) {
        animation-delay: 0.6s;
    }

    .dot:nth-child(5) {
        animation-delay: 0.8s;
    }



    .checkmark-container {
        color: #0d6efd;
        font-size: 190px;
        justify-content: center;
        animation: popIn 0.5s ease forwards;
    }

    .inAnimation-container {
        display: flex;
        margin-left: -70px;
        color: #fdfeff;
        font-size: 190px;
        justify-content: center;
        animation: popIn 0.5s ease forwards;
    }

    @keyframes dot-blink {

        0%,
        20%,
        100% {
            transform: translateY(0);
            opacity: 0.6;
        }

        50% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }

    .user-message-container {
        background-color: #0d6efd;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 100%;
        margin: 10px auto;
    }




    .user-message-container2 {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 100%;
        margin: 10px auto;
    }

    .bouncing {
        animation: bounce 1s ease-in-out infinite;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    #waitingAnimation-container {
        opacity: 0;
        transition: opacity 2s;
        font-size: 24px;
        color: rgb(0, 0, 0);
        background-color: #ffffff;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 35px;
    }



    .fade-in-out {
        transition: opacity 2s;
    }

    #user-info {
        opacity: 1;
        transition: color 2s ease;
    }
</style>

<body>

    @include('topbar')

    <div class="content">
        <div class="container mt-5">
            <div class="d-flex flex-column justify-content-center full-height">
                @foreach($destinations as $destination)
                <button class="destination-button btn btn-primary shadow btn-lg btn-block btn-animated py-9 mb-10 custom-font-size" onclick="pipeq._register({{ $destination->id }});" title="{{ __($destination->description) }}">
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
            <div id="waitingAnimation-container">
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
    <div id="inPage" class="page">
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


    <!-- // todo: Correct this javascript, it's bad -->
    <script>
        //debugging
        // displayStatusWaiting('ticket');
        // displayStatusIn('ticket');
        // displayStatusEnd('ticket');

        class PipeQ {
            constructor() {
                @if(!is_null($token))
                var token = '{{ $token }}';
                this.register = Echo.channel(`register.${token}`);
                this._listen();
                @endif
            }

            _listen() {
                this.register.listen('UpdateUserAboutHisTicket', (e) => {
                    console.log(e);
                    displayStatusWaiting(e.ticket);
                })

                this.register.listen('NotifyEndedTicketUser', (e) => {
                    console.log('Your ticket has ended');

                    axios.post('{!! route("_clear"); !!}')
                        .then(response => {
                            console.log(response.data.message);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                })
            }

            _register(destination_id) {
                axios.post(`/register/${destination_id}`)
                    .then(response => {
                        // Listen for response with channel name
                        console.log(response.data.message + ' token: ' + response.data.channel);

                        // if channel name is received, subscribe to it
                        if (response.data.channel) {
                            this.register = Echo.channel(`register.${response.data.channel}`);

                            //* console.log(response)
                            //* const ticket_nr = response.data.ticket_nr;
                            //* document.getElementById('waitingPage-ticket_nr').innerHTML = ticket_nr;
                            this._listen();
                            displayStatusWaiting('ticket');
                        } else {
                            console.error('Channel name not received');
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
        }

        document.addEventListener('DOMContentLoaded', function() {
            pipeq = new PipeQ();
        });

        function displayStatusWaiting(ticket) {
            document.getElementById('waitingPage').style.display = 'flex';
            const waitingPage = document.getElementById('waitingPage');
            waitingPage.classList.toggle('show');
        }

        function displayStatusIn(ticket) {
            const inPage = document.getElementById('inPage');
            inPage.classList.toggle('show');
            document.getElementById('ticketWorkstation').innerHTML = ticket.workstation;
            document.getElementById('ticketUser').innerHTML = ticket.user;
        }

        function displayStatusEnd(ticket) {
            alert('unimplemented');
        }

        setTimeout(function() {
            document.getElementById("waitingAnimation-container").style.opacity = "1";
        }, 2000);
    </script>
</body>

</html>