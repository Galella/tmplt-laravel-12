<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OfficeController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Dashboard route - protected
Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth')->name('dashboard');

// User management routes - protected
Route::resource('users', UserController::class)->middleware('auth');

// Role management routes - protected
Route::resource('roles', RoleController::class)->middleware('auth');

// Office management routes - protected
Route::resource('offices', OfficeController::class)->middleware('auth');

// Optional: Add registration routes if needed later
// Route::post('/register', [RegisterController::class, 'register']);
