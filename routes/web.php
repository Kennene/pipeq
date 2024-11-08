<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\AdministratorController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DisplayController;

use App\Http\Controllers\UserRegisterController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

require __DIR__ . '/auth.php';

Route::get("/", [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('user');
Route::get("/coordinator", [CoordinatorController::class, 'index'])->middleware(['auth', 'verified'])->name('coordinator');
Route::get("/administrator", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator');
Route::get("/display", [DisplayController::class, 'index'])->middleware(['auth', 'verified'])->name('display');


// API for client -> server communication
// todo: przenieśc do routes/api.php
Route::any("/register/{destination_id}", [UserController::class, 'register'])->middleware(['auth', 'verified'])->name('_register');
Route::any("/move/{ticket_id}/{workstation_id}/{status_id?}", [CoordinatorController::class, 'move'])->middleware(['auth', 'verified'])->name('_move');
Route::any("/end/{ticket_id}", [CoordinatorController::class, 'end'])->middleware(['auth', 'verified'])->name('_end');
