<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{page}', [App\Http\Controllers\AdminController::class, 'index']);
