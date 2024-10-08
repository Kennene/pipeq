<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\AdministratorController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get("/", [UserController::class, 'index'])->name('user.user');

// todo: dodać pewien sposób uwierzytelnienia do koordynatorów
// Route::get("/coordinator", [CoordinatorController::class, 'index'])->middleware(['auth', 'verified'])->name('coordinator.coordinator');
Route::get("/coordinator", [CoordinatorController::class, 'index'])->name('coordinator.coordinator');

// todo: dodać pewien sposób uwierzytelnienia do koordynatorów
// Route::get("/administrator", [AdministratorController::class, 'index'])->middleware(['auth', 'verified'])->name('administrator.administrator');
Route::get("/administrator", [AdministratorController::class, 'index'])->name('administrator.administrator');