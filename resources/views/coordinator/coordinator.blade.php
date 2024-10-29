<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> <!-- Potrzebne do wysyłania zapytań POST -->
    <title>Coordinator</title>
</head>

<body>

    @include('topbar')

    <div id="app">
        <Coordinator></Coordinator>
    </div>

</body>


<script>

    class PipeQ {
        constructor() {
            const channel = Echo.private('register');
            this.register = channel;
        }

        _move($ticket_id, $destination) {
            axios.post(`/move/$`)
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
    PipeQ = new PipeQ();
});


</script>

</html>