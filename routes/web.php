<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\AdministratorController;

Route::get('/', function () {
    return view('welcome');
});


Route::get("/", [UserController::class, 'index'])->name('user.user');


// todo: dodać pewien sposób uwierzytelnienia do koordynatorów
// Route::get("/coordinator", [CoordinatorController::class, 'index'])->middleware(['auth', 'verified'])->name('coordinator.coordinator');
Route::get("/coordinator", [CoordinatorController::class, 'index'])->name('coordinator.coordinator');


// todo: dodać pewien sposób uwierzytelnienia do koordynatorów
// Route::get("/administrator", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator.administrator');
Route::get("/administrator", [AdministratorController::class, 'index'])->name('administrator.administrator');