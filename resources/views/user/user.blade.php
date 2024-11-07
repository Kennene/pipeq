<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/user.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    @include('topbar')

    <div class="container mt-5">
        
            
    @foreach($destinations as $destination)
                            <div class="d-flex flex-column justify-content-center  full-height">
                            
                    <button type="button" class="btn btn-primary shadow btn-lg btn-block btn-animated py-9 mb-10 custom-font-size" style="height: 30vh;" title="{{ __($destination->description) }}" onclick="pipeq._register({{ $destination->id }});">{{ __($destination->name) }}</button>
    @endforeach
                </div></div>
          

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
        this.register.listen('RegisterNewTicket', function(e) {
            console.log(e);
        })

        this.register.listen('UpdateUserAboutHisTicket', function(e) {
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