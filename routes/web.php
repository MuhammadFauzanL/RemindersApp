<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TaskController;

// ============== AUTH ==============

// Halaman login
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

// Proses login
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process')
    ->middleware('guest');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Halaman register
Route::get('/register', [RegisterController::class, 'showRegisterForm'])
    ->name('register')
    ->middleware('guest');

// Proses register
Route::post('/register', [RegisterController::class, 'register'])
    ->name('register.process')
    ->middleware('guest');

// ============== DASHBOARD ==============

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// ============== TASKS ==============

// tambah tugas
Route::post('/tasks', [TaskController::class, 'store'])
    ->middleware('auth')
    ->name('tasks.store');

// toggle selesai/belum
Route::post('/tasks/{task}/toggle', [TaskController::class, 'toggle'])
    ->middleware('auth')
    ->name('tasks.toggle');

// hapus tugas
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
    ->middleware('auth')
    ->name('tasks.destroy');

// form edit tugas
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])
    ->middleware('auth')
    ->name('tasks.edit');

// proses update tugas
Route::put('/tasks/{task}', [TaskController::class, 'update'])
    ->middleware('auth')
    ->name('tasks.update');
