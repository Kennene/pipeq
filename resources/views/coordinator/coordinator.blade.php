<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> <!-- Potrzebne do wysyłania zapytań POST -->
    <title>Coordinator</title>
</head>

<body>

    @include('topbar')

    <button class="btn btn-secondary" onclick="PipeQ._move(3, 2, 4);">Move ticket 3 to workstation 2 with status 4</button>

    <div id="app">
        <Coordinator></Coordinator>
    </div>

</body>


<script>

class PipeQ {
    constructor() {
        
    }

    _move(ticket_id, workstation_id, status_id = 2) {
        axios.post(`/move/${ticket_id}/${workstation_id}`, {
            status_id: status_id
        })
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