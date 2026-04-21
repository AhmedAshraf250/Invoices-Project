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
    Route::get('organizations/{organization}/products', [OrganizationController::class, 'getProducts'])->name('organizations.products.index');

    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('invoices/preview-calculation', [InvoiceController::class, 'previewCalculation'])->name('invoices.preview-calculation');
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status.update');
    Route::post('invoices/{invoice}/attachments', [InvoiceController::class, 'storeAttachment'])->name('invoices.attachments.store');
    Route::get('invoices/{invoice}/attachments/{attachment}/download', [InvoiceController::class, 'downloadAttachment'])->name('invoices.attachments.download');
    Route::delete('invoices/{invoice}/attachments/{attachment}', [InvoiceController::class, 'destroyAttachment'])->name('invoices.attachments.destroy');
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
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
