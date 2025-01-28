<nav id="topbar" class="bg-white py-2 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <img id="app-logo" src="/images/logo.webp" class="h-12 w-auto" alt="App Logo" />
            <h1 id="app-name" class="text-xl font-semibold">{{ env('APP_NAME') }}</h1>
        </div>

        <div class="relative">
            <!-- Przycisk otwierający menu -->
            <button id="lang-button" type="button"
                class="bg-blue-500 text-white font-medium py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 flex items-center"
                aria-haspopup="true" aria-expanded="false">
                Language
                <svg class="ml-2 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path
                        d="M5.293 7.293a1 1 0 010 1.414L10 13.414l4.707-4.707a1 1 0 00-1.414-1.414L10 10.586 6.707 7.293a1 1 0 00-1.414 0z" />
                </svg>
            </button>

            <!-- Dropdown menu - ukryte domyślnie -->
            <div id="lang-menu"
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg py-1 z-50 hidden"
                aria-labelledby="dropdownMenuLink">
                @foreach (config('app.available_locales') as $key => $locale)
                    @if ($key === App::getLocale())
                        <a href="{{ route('locale.set', $key) }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100 font-semibold">
                            {{ $locale }}
                        </a>
                    @else
                        <a href="{{ route('locale.set', $key) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            {{ $locale }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</nav>