<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function set(Request $request, string $locale)
    {
        // if locale exists in available locales, set it
        if (array_key_exists($locale, config('app.available_locales'))) {
            session()->put('locale', $locale);
        } else {
            // if locale doest not exist, set fallback locale
            session()->put('locale', config('app.fallback_locale'));
        }

        return back()->withCookie(cookie('locale', $locale));
    }
}
