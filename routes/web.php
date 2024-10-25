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

require __DIR__ . '/auth.php';


Route::get("/", [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('user');
Route::get("/coordinator", [CoordinatorController::class, 'index'])->middleware(['auth', 'verified'])->name('coordinator');
Route::get("/administrator", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator');
//Route::get("/display", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator.administrator');
Route::get("/display", [DisplayController::class, 'index'])->middleware(['auth', 'verified'])->name('display');


// API for client -> server communication

Route::post("/register/{destination}", [UserController::class, 'register'])->middleware(['auth', 'verified']);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// todo: przenieśc do routes/api.php
Route::post("/register/{destination}", [UserController::class, 'register'])->middleware(['auth', 'verified'])->name('register');
Route::post("/move/{ticket_id}/{destination}", [CoordinatorController::class, 'move'])->middleware(['auth', 'verified'])->name('move');
Route::post("/register/{destination}", [UserController::class, 'register'])->middleware(['auth', 'verified']);
Route::post("/move/{ticket_id}/{destination}", [CoordinatorController::class, 'move'])->middleware(['auth', 'verified']);
