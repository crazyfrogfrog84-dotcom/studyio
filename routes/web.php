<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::resource('portfolios', PortfolioController::class)->only([
    'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
]);
Route::get('/portfolios/{portfolio}/confirm-delete', [PortfolioController::class, 'confirmDelete'])->name('portfolios.confirm-delete');
Route::get('/portfolios/{portfolio}/templates', [PortfolioController::class, 'templates'])->name('portfolios.templates.edit');
Route::put('/portfolios/{portfolio}/template', [PortfolioController::class, 'selectTemplate'])->name('portfolios.templates.update');
