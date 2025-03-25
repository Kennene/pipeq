<!DOCTYPE html>
<html lang="pl">

<head>
    @include('layouts.head') {{-- poprawka: używaj @include, nie @extends --}}
    <title>Not available</title>
</head>

<body>

    @include('topbar')

    <div id="app">
        <time-restricted-card
            :destinations='@json($destinations)'
            :translations='@json($translations)'
        >
        </time-restricted-card>
    </div>

    @stack('scripts')
</body>

</html>

@push('scripts')
    @vite('resources/js/app.js')
@endpush
