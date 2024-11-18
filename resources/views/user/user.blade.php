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
            @foreach($destinations as $destination)
                <div class="d-flex flex-column justify-content-center full-height">
                    <button type="button" class="btn btn-primary shadow btn-lg btn-block btn-animated py-9 mb-10 custom-font-size" onclick="showLoading(); toggleOverlay(); pipeq._register({{ $destination->id }});" style="height: 30vh;" title="{{ __($destination->description) }}" 
                    onclick="pipeq._register({{ $destination->id }});">{{ __($destination->name) }}</button>
                </div>
            @endforeach
        </div>
        
      

 


    <div id="overlay" class="overlay">
        <div class="loading-container">
            <div class="user-info d-flex flex-column align-items-center fade-in-out" id="user-info">
                <div class="checkmark-container">
                    <i class="bi bi-check-circle-fill" ></i>
                </div>
                
            </div>
            <div class="user-message-container">
                    <div class="user-message">
                        <p class="username">{{auth()->user()?->name}} dziękujemy za zgłoszenie</p>
                        <p class="waiting-message">Proszę czekać na swoją kolej</p>
                    </div>
                </div>
           
            <div class="loading-container2" id="loading-container2">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
           
        </div>
    </div>

    <div id="overlay2" class="overlay">
        <div class="loading-container2">
            <div class="user-info d-flex flex-column align-items-center">
                <div class="checkmark-container2">
                    <i class="bi bi-box-arrow-in-right bouncing"></i>
                </div>
                <div class="user-message-container2">
                    <div class="user-message2">
                         <p class="username2">
                            <span id='ticketUser'> User </span><span> zapraszamy!</span> </p>
                        <p id="ticketWorkstation" class="waiting-message2">Stanowisko </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script>

class PipeQ {
    constructor() {
        // todo: automatically join channel if user has token
        
        //* none of the below works
        // $.removeCookie('ticket_token');
        // document.cookie = "ticket_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    _listen() {
        this.register.listen('UpdateUserAboutHisTicket', (e) => {
            console.log(e);
            toggleOverlay2(e.ticket);
            showLoading();
        })

        this.register.listen('EndUserTicket', (e) => {
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
                    this._listen();
                } else {
                    console.error('Channel name not received');
                }
            })
            .catch(error => {
                // if error message is handled by the server, display it, otherwise display generic error
                if(error.response.data.error) {
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
 

        function showLoading() {
            document.getElementById('overlay').style.display = 'flex';
        }

        function toggleOverlay() {
            const overlay = document.getElementById('overlay');
            overlay.classList.toggle('show');
        }

        function toggleOverlay2(ticket) {
            const overlay2 = document.getElementById('overlay2');
            overlay2.classList.toggle('show');
            document.getElementById('ticketWorkstation').innerHTML=ticket.workstation;
            document.getElementById('ticketUser').innerHTML=ticket.user;
        }

        setTimeout(function() {
    document.getElementById("user-info").style.color = "green"; 
}, 5000);

   
    setTimeout(function() {
        document.getElementById("loading-container2").style.opacity = "1";
    }, 2000);



</script>
    </script>
</body>
</html>
