<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\AdministratorController;

use App\Http\Controllers\UserRegisterController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get("/", [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('user.user');
Route::get("/coordinator", [CoordinatorController::class, 'index'])->middleware(['auth', 'verified'])->name('coordinator.coordinator');
Route::get("/administrator", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator.administrator');



// API for client -> server communication

Route::post("/register/{destination}", [UserController::class, 'register'])->middleware(['auth', 'verified']);