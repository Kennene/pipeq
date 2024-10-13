<?php

use Illuminate\Support\Facades\Broadcast;

// Domyślna konfiguracja kanału stworzona przez framework
// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('register', function () {
    return true;
});

Broadcast::channel('coordinator', function () {
    
});

Broadcast::channel('display', function () {
    
});