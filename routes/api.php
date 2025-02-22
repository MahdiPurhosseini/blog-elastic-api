<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\\Http\\Controllers\\Post')->group(function () {
    Route::handler('posts');
});
