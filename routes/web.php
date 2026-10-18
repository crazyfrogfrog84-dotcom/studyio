<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'createLinkRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:password-reset-links')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'createNewPassword'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'storeNewPassword'])->middleware('throttle:password-reset-submissions')->name('password.update');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('portfolios', PortfolioController::class)->only([
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
    ]);
    Route::get('/portfolios/{portfolio}/confirm-delete', [PortfolioController::class, 'confirmDelete'])->name('portfolios.confirm-delete');
    Route::get('/portfolios/{portfolio}/templates', [PortfolioController::class, 'templates'])->name('portfolios.templates.edit');
    Route::put('/portfolios/{portfolio}/template', [PortfolioController::class, 'selectTemplate'])->name('portfolios.templates.update');
});
