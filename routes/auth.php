<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
    ],
], function () {

    require base_path('vendor/laravel/fortify/routes/routes.php');
});
