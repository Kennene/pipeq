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


// todo: clean up this code. user's don't have dashboards or need to register
Route::get('/dashboard', function () {return view('dashboard');})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/logout', function () {Auth::logout();return redirect('/login');})->name('logout');
require __DIR__ . '/auth.php';


Route::get('/language/{locale}', [LanguageController::class, 'set'])->name('locale.set');


Route::get("/", [UserController::class, 'index'])
    ->middleware('role:'.Role::USER)
    ->name('user');

// todo: remove this route
Route::get("/2", [UserController::class, 'index2'])->middleware('role:'.Role::USER);

Route::get("/display", [DisplayController::class, 'index'])
    ->middleware([
        'auth',
        'role:'.Role::DISPLAY
    ])
    ->name('display');


Route::get("/coordinator", [CoordinatorController::class, 'index'])
    ->middleware([
        'auth',
        'role:'.Role::COORDINATOR
    ])
    ->name('coordinator');


Route::get("/administrator", [AdministratorController::class, 'index'])
    ->middleware([
        'auth',
        'role:'.Role::ADMINISTRATOR
    ])
    ->name('administrator');

    

//* API for client -> server communication
// todo: change any to post at the end of the project

//* user space
Route::middleware('role:'.Role::USER)->group(function () {
    Route::controller(TicketController::class)->group(function () {
        Route::any("/register/{destination_id}", 'register')->name('_register');
        Route::any("/endByUser/{ticket_token?}", 'endByUser')->name('_endByUser');
        Route::any("/clearStorage", 'clearStorage')->name('_clear');
    });
});

//* coordinator space
Route::middleware(['auth', 'verified', 'role:'.Role::COORDINATOR])->group(function () {
    Route::controller(TicketController::class)->group(function () {
        Route::any("/move/{ticket_id}/{workstation_id?}/{status_id?}", 'move')->name('_move');
        Route::any("/end/{ticket_id}", 'end')->name('_end');
    });
});