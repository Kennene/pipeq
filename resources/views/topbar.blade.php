@vite(['resources/css/topbar.css'])

<nav id="topbar" class="navbar py-2">
    <div class="container-fluid">
        <div class="navbar-brand d-flex align-items-center">
            <img id="app-logo" src="/images/logo.png" class="img-fluid" style="height: 50px;" />
            <div class="app-name ms-2">
                <h1 id="app-name" class="h1 mb-0 ml-1">{{ env('APP_NAME') }}</h1>
            </div>
        </div>

        <div class="dropdown">
            <a id="lang-button" class="btn btn-primary dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Language</a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                @foreach (config('app.available_locales') as $key => $locale)
                    @if ($key === App::getLocale())
                        <a class="dropdown-item active" href="{{ route('locale.set', $key) }}">{{ $locale }}</a>
                    @else
                        <a class="dropdown-item" href="{{ route('locale.set', $key) }}">{{ $locale }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        
    </div>
</nav>