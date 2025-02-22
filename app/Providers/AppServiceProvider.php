<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerHandler();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

    protected function registerHandler()
    {
        Route::macro('handler', function ($prefix) {
            $singular = Str::singular($prefix);
            $parameterName = Str::camel($singular);
            $name = Str::studly($singular);

            Route::get($prefix, 'Index' . $name);
            Route::post($prefix, 'Store' . $name);
            Route::put($prefix . '/{' . $parameterName . '}', 'Update' . $name);
            Route::delete($prefix . '/{' . $parameterName . '}', 'Destroy' . $name);
            Route::get($prefix . '/{' . $parameterName . '}', 'Show' . $name);
        });
    }
}
