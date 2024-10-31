<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Domyślna konfiguracja kanału stworzona przez framework
// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('register.{user_id}', function ($user, int $user_id) {
    return (int) $user->id === (int) $user_id;
});

// todo: dodanie autoryzację tylko do koordynatorów i przeglądarek z odpowiednim certyfikatem
Broadcast::channel('display', function () {
    return true;
});