<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('register.{ticket_token}', function () {
    return true;
});

Broadcast::channel('display', function () {
    return true;
});
