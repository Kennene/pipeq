<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/user.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>User</title>
</head>
<body>

    @include('topbar')
    
    User

    <div id="buttons-container">
        <button class="button" id="room1">Room 1</button>
        <button class="button" id="room2">Room 2</button>
    </div>

    <div class="container-lg">
        <div class="card mt-2">
            <button type="button" class="btn btn-success">Working bootstrap</button>
        </div>
    </div>

</body>
<script>

    class PipeQ {
        constructor() {
            const channel = Echo.private(`register.{{ Auth::user()->id }}`);
            this.register = channel;
            this._listen();
        }

        _listen() {
            this.register.listen('UserRegister', function(e) {
                console.log(e);
            })
        }

        _register(destination_id) {
            axios.post(`/register/${destination_id}`)
                .then(response => {
                    @if(env('APP_DEBUG'))
                        console.log(response);
                    @endif
                })
                .catch(error => {
                    console.error(error.response.data.error);
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        pipeq = new PipeQ();
        
        $('#room1').click(function() {
            pipeq._register(1);
        });

        $('#room2').click(function() {
            pipeq._register(2)
        });
    });

</script>

</html>