<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__.'/auth.php';

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth'],
], function (): void {

    Route::resource('organizations', OrganizationController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('invoices', InvoiceController::class);
});

// ========================================================================= //

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {

    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/{page}', [AdminController::class, 'index'])
        ->where('page', '^(?!login$|logout$|register$|forgot-password$|two-factor-challenge$|up$).+')
        ->name('page.show');
});
