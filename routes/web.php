<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::namespace('App\\Http\\Controllers\\Post')->group(function () {
    Route::handler('posts');
});
