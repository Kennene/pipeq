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
        <button class="button">Room 1</button>
        <button class="button">Room 2</button>
    </div>


</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Echo.channel('chat')
        //     .whisper('MessageSent', {
        //         name: "this.user.name"
        //     });

            Echo.channel('register')
                .listen('UserRegistered', function(e) {
                    console.log(e);
                });
    });

</script>

</html>