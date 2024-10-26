<!DOCTYPE html>
<html lang="pl">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/coordinator.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <title>Coordinator</title>
</head>

<body>

    @include('topbar')

    <div id="app">
        <Coordinator></Coordinator>
    </div>

</body>

</html>