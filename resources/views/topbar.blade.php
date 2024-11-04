@vite(['resources/css/topbar.css'])

<nav id="topbar" class="navbar py-2 bg-dark">
    <div class="container-fluid">
        <div class="navbar-brand d-flex align-items-center">
            <img id="app-logo" src="/images/logo.png" class="img-fluid" style="height: 40px;" />
            <div class="app-name ms-2">
                <h1 class="h4 mb-0 text-light">{{ env('APP_NAME') }}</h1>
            </div>
        </div>

        <div class="dropdown">
            <a class="btn btn-primary dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Language</a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                @foreach (config('app.available_locales') as $locale => $language)
                    @if ($locale === App::getLocale())
                        <a class="dropdown-item active" href="">{{ $language }}</a>
                    @else
                        <a class="dropdown-item" href="">{{ $language }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        
    </div>
</nav>