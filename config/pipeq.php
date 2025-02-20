<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum ticket number
    |--------------------------------------------------------------------------
    |
    | This value is the maximum ticket number that can be assigned to a ticket.
    | Note, that this is not id of the ticket, nor anything related to it. It is
    | simply abstract number given out to a user for his information only. It is
    | meant to be easly indentifiable by a user.
    |
    */

    'max_ticket_number' => env('MAX_TICKET_NUMBER', 99),

    /*
    |--------------------------------------------------------------------------
    | Sound on display view
    |--------------------------------------------------------------------------
    |
    | This value is used to determine if sound should be played by display view.
    | True means sound will be played, false means it won't. False is the default
    | vaule, as playing sounds may be distracting in many scenerios.
    | If you would like to change the notification sound, you need to replace
    | public/notification.mp3 with your own sound file.
    |
    */


    'is_display_sound' => env('IS_DISPLAY_SOUND', false),
    'is_coordinator_sound' => env('IS_DISPLAY_SOUND', false),
    'display_sound_path' => env('DISPLAY_SOUND_PATH', env('APP_URL') . "/notification.mp3"),

];
