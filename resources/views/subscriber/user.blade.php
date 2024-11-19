<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/js/user.js'])
    @pipeQColors
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<style>
</style>

<body>

    @include('topbar')

    <div class="content" style="background-color: red;">
        <div class="container">
            @foreach($destinations as $destination)
            <div class="d-flex flex-column justify-content-center full-height m-20">
                <button
                    class="btn btn-primary shadow btn-lg btn-block btn-animated custom-font-size"
                    onclick="pipeq._register({{ $destination->id }});"
                    title="{{ __($destination->description) }}">
                    {{ __($destination->name) }}
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{ $destinations }}

    <div id="app">
        <page-swapper :titles="{{ json_encode($destinations) }}"></page-swapper>
    </div>


</body>

<script>
    class PipeQ {
        constructor() {
            // todo: automatically join channel if user has token
            console.log('PipeQ initialized');
        }

        _listen() {
            this.register.listen('UpdateUserAboutHisTicket', (e) => {
                console.log(e.message, e.ticket);
            })

            // todo: why it spams as well as _updateTicketStatusByRequest?
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
            console.log('Registering to destination ' + destination_id);

            axios.post(`/register/${destination_id}`)
                .then(response => {
                    console.log(response.data);

                    // if channel name is received, subscribe to it
                    if (response.data.channel) {
                        const channel = response.data.channel;
                        this.register = Echo.channel(`register.${channel}`);
                        this._listen();
                        this._updateTicketStatusByRequest();
                    } else {
                        console.error('Channel name not received');
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        _updateTicketStatusByRequest(channel = '') {
            // todo: why it spams multiple requests, but only when ticket is already registered?
            axios.post(`/status/${channel}`)
                .catch(error => {
                    console.log(error);
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        pipeq = new PipeQ();
    });
</script>

</html>