<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use App\Models\Color;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('pipeQColors', function () {
            $colors = Color::all();

            $pipeQColors = $colors->map(function ($colors) {
                return "--{$colors->name}: {$colors->hex_code};";
            })->implode(' ');

            return "<style>:root { $pipeQColors }</style>";
        });
    }
}
