<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/display.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display</title>
</head>
<body>
    
    Display

</body>

<script>,
    document.addEventListener('DOMContentLoaded', function() {

        // todo: po połączeniu display poproś o wszystkie otwarte tickety

        const channel = Echo.private('display');

        // todo: handle event UserMove
        channel.listen('UserMove', function(e) {
            console.log(e);
        })
    });
</script>

</html>