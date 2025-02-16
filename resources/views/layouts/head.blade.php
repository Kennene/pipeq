<!-- Scripts -->
@vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
<script src="{{ asset('js/jquery/jquery-3.7.1.min.js') }}" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('favicon.ico') }}">

<!-- Inter font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

<!-- PipeQ unified colors -->
@pipeQColors