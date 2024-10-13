<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/user.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
</head>
<body>
    
    User

    <div id="buttons-container">
        <button class="button" id="room1">Room 1</button>
        <button class="button" id="room2">Room 2</button>
    </div>


</body>

<!-- // todo: Zamień to na plik dostarczany przez serwer laravela -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>

    $('#room1').click(function() {
        axios.post('/register/{{ auth()->user()->id }}')
            // todo: lepiej ogarnąć kody błędów
            .then(response => {
                // console.log(response.data);
            })
            .catch(error => {
                console.error(error);
            })
    });

        
    document.addEventListener('DOMContentLoaded', function() {
        const channel = Echo.private('register');

        channel.listen('UserRegister', function(e) {
            console.log(e);
        })

        // todo: handle event UserEnter
        channel.listen('UserEnter', function(e) {
            console.log(e);
        })

        

        // Śmietnik z testowania channelów, może się przydać w przyszłości, na razie nie usuwać
            // Echo.channel('chat')
            //     .whisper('MessageSent', {
            //         name: "this.user.name"
            //     });
            //const channel = Echo.private('register');
            // channel.listen('listenForWhisper', function(e) {
            //             console.log(e);
            //         })
            //         .whisper('register', {
            //             name: "lol"
            //         });
            // channel.listenForWhisper('register', function(e) {
            //     console.log(e);
            // })
            // onmousemove = (e) => {
            //     channel.whisper('register', {
            //         name: "lol"
            //     });
            // };
            // Echo.channel('your-channel')
            //     .listen('.your-event-name', (event) => {
            //         console.log(event.message); // Output: 'Hello from Laravel!'
            //     });
    });

</script>

</html>