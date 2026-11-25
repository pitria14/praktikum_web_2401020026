<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\LoginController;

// Route login
Route::get('/login', [LoginController::class, 'show'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.proses');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

// Route buku untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    // Admin dan pembaca dapat melihat daftar buku
    Route::get('/buku', [BukuController::class, 'index'])
        ->name('buku.index');

    // Hanya admin yang dapat mengelola buku
    Route::middleware('can:kelola-buku')->group(function () {

        Route::resource('buku', BukuController::class)
            ->except(['index', 'show']);

    });
});
