<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\AdministratorController;

require __DIR__ . '/auth.php';

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->back()->with('success', 'You have been logged out');
})->name('logout');

Route::get('/language/{locale}', [LanguageController::class, 'set'])->name('locale.set');


Route::middleware('role:' . Role::USER)->group(function () {
    Route::get("/", [UserController::class, 'index'])->name('user');

    Route::controller(TicketController::class)->group(function () {
        Route::post("/register/{destination_id}/{reason_id?}", 'register')->name('_register');
        Route::post("/status/{ticket_token?}", 'status')->name('_status');
        Route::post("/endByUser/{ticket_token?}", 'endByUser')->name('_endByUser');
        Route::post("/clearStorage", 'clearStorage')->name('_clear');
        Route::post("/reason/{reason_id}/{ticket_token?}", 'updateReason')->name('_updateReason');
    });
});


Route::middleware(['auth', 'verified', 'role:' . Role::DISPLAY])->group(function () {
    Route::get("/display", [DisplayController::class, 'index'])->name('display');
});


Route::middleware(['auth', 'verified', 'role:' . Role::COORDINATOR])->group(function () {
    Route::get("/coordinator", [CoordinatorController::class, 'index'])->name('coordinator');

    Route::controller(TicketController::class)->group(function () {
        Route::post("/move/{ticket_id}/{workstation_id?}/{status_id?}", 'move')->name('_move');
        Route::post("/changeDestination/{ticket_id}/{destination_id}", 'changeDestination')->name('_changeDestination');
        Route::post("/end/{ticket_id}", 'end')->name('_end');
        Route::post("/endAll/{destination_id?}", 'endAll')->name('_endAll');
    });
});


Route::middleware(['auth', 'verified', 'role:' . Role::ADMINISTRATOR])->group(function () {
    Route::controller(AdministratorController::class)->group(function () {
        Route::get("/administrator", 'index')->name('administrator');
        Route::post("/updateSchedules", 'updateSchedule')->name('_updateSchedules');
        Route::post("/updateLanguages", 'updateLanguages')->name('_updateLanguages');
    });
});