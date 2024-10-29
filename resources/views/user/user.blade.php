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
    

    <div id="buttons-container" class="mt-2 ml-3">
        @foreach($destinations as $destination)
            <button class="btn btn-info ml-2" title="{{ $destination->description }}" onclick="pipeq._register({{ $destination->id }});">{{ $destination->name }}</button>
        @endforeach
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
    });

</script>

</html>