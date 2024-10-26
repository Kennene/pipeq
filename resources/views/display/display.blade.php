<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/display.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display</title>
</head>

<style>

body {
    margin: 0;
    background-color: var(--main);
}

#secondary {
    width: 30vw;
    height: 30vh;
    background-color: {{ $color::secondary }};
}

#addintional {
    width: 30%;
    height: 15vh;
    float: right;
    background-color: {{ $color::additional }};
}

#details {
    width: 30%;
    height: 15vh;
    float: right;
    background-color: {{ $color::details }};
}

</style>

<body>

    @include('topbar')
    
    <div id="secondary">
        <div id="details"></div>
        <div id="addintional"></div>
    </div>

</body>

<script>
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