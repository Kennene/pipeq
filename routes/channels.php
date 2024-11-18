<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('register.{ticket_token}', function () {
    return true;
});

// todo: dodanie autoryzacji przeglądarek z odpowiednim certyfikatem
Broadcast::channel('display', function () {
    return true;
});