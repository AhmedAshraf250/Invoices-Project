<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__.'/auth.php';

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth'],
], function (): void {

    Route::get('users', [UserController::class, 'index'])->middleware('can:users.view')->name('users.index');
    Route::post('users', [UserController::class, 'store'])->middleware('can:users.create')->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('can:users.view')->name('users.show');
    Route::patch('users/{user}', [UserController::class, 'update'])->middleware('can:users.update')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('can:users.delete')->name('users.destroy');

    Route::get('roles', [RoleController::class, 'index'])->middleware('can:roles.view')->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->middleware('can:roles.create')->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->middleware('can:roles.view')->name('roles.show');
    Route::patch('roles/{role}', [RoleController::class, 'update'])->middleware('can:roles.update')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('can:roles.delete')->name('roles.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])->middleware('can:permissions.view')->name('permissions.index');

    Route::resource('organizations', OrganizationController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middlewareFor('index', 'can:organizations.view')
        ->middlewareFor('store', 'can:organizations.create')
        ->middlewareFor('update', 'can:organizations.update')
        ->middlewareFor('destroy', 'can:organizations.delete');
    Route::get('organizations/{organization}/products', [OrganizationController::class, 'getProducts'])
        ->middleware('can:organizations.view')
        ->name('organizations.products.index');

    Route::resource('products', ProductController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middlewareFor('index', 'can:products.view')
        ->middlewareFor('store', 'can:products.create')
        ->middlewareFor('update', 'can:products.update')
        ->middlewareFor('destroy', 'can:products.delete');

    Route::post('invoices/preview-calculation', [InvoiceController::class, 'previewCalculation'])->middleware('can:invoices.create')->name('invoices.preview-calculation');
    Route::get('invoices/export/excel', [InvoiceController::class, 'exportExcel'])->middleware('can:invoices.export')->name('invoices.export.excel');

    Route::get('invoices/status/{status}', [InvoiceController::class, 'status'])
        ->whereIn('status', ['paid', 'partial', 'unpaid'])
        ->middleware('can:invoices.view')
        ->name('invoices.status');

    Route::get('invoices/archived', [InvoiceController::class, 'archived'])->middleware('can:invoices.archived')->name('invoices.archived');
    Route::patch('invoices/{invoice}/archive', [InvoiceController::class, 'archive'])->middleware('can:invoices.archive')->name('invoices.archive');
    Route::patch('invoices/{invoiceId}/restore', [InvoiceController::class, 'restore'])->middleware('can:invoices.restore')->name('invoices.restore');
    Route::delete('invoices/{invoiceId}/force-delete', [InvoiceController::class, 'forceDelete'])->middleware('can:invoices.delete')->name('invoices.force-delete');

    Route::get('invoices/{invoiceId}/print', [InvoiceController::class, 'print'])->middleware('can:invoices.view')->name('invoices.print');
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->middleware('can:invoices.change-status')->name('invoices.status.update');

    Route::post('invoices/{invoice}/attachments', [InvoiceController::class, 'storeAttachment'])->middleware('can:invoice-attachments.create')->name('invoices.attachments.store');
    Route::get('invoices/{invoice}/attachments/{attachment}/download', [InvoiceController::class, 'downloadAttachment'])->middleware('can:invoices.view')->name('invoices.attachments.download');
    Route::get('invoices/{invoice}/attachments/{attachment}/view', [InvoiceController::class, 'viewAttachment'])->middleware('can:invoices.view')->name('invoices.attachments.view');
    Route::delete('invoices/{invoice}/attachments/{attachment}', [InvoiceController::class, 'destroyAttachment'])->middleware('can:invoice-attachments.delete')->name('invoices.attachments.destroy');

    Route::resource('invoices', InvoiceController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->middlewareFor('index', 'can:invoices.list')
        ->middlewareFor(['create', 'store'], 'can:invoices.create')
        ->middlewareFor('show', 'can:invoices.view')
        ->middlewareFor('destroy', 'can:invoices.delete');
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
