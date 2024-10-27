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

    function register(destination) {
        axios.post(`/register/${destination}`)
            // todo: lepiej ogarnąć kody błędów
            .then(response => {
                if('{{ env('APP_DEBUG') }}' === '1') {
                    console.log(response.data);
                }
            })
            .catch(error => {
                console.error(error);
            })
    }

    $('#room1').click(function() {
        register(1)
    });

    $('#room2').click(function() {
        register(2)
    });

        
    document.addEventListener('DOMContentLoaded', function() {
        const channel = Echo.private('register');

        channel.listen('UserRegister', function(e) {
            console.log(e);
        })

        // todo: handle event UserMove
        channel.listen('UserMove', function(e) {
            console.log(e);
        })
    });

</script>

</html>