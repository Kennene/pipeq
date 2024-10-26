<style>
    :root {
        --main: {{ \App\Models\Color::main }};
        --secondary: {{ \App\Models\Color::secondary }};
        --additional: {{ \App\Models\Color::additional }};
        --details: {{ \App\Models\Color::details }};
        --white: {{ \App\Models\Color::white }};
        --black: {{ \App\Models\Color::black }};
    }
</style>

@vite(['resources/css/topbar.css'])

<nav id="topbar" class="p-4 flex justify-between items-center shadow-lg relative">
    <div class="flex items-center">
        <img src="/images/logo.png" alt="Logo" class="h-12" />
        <h1 class="ml-4 text-xl font-bold">PipeQ</h1>
    </div>
</nav>