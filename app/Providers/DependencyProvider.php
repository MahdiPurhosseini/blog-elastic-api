<?php

namespace App\Providers;

use App\Interfaces\PostInterface;
use App\Repositories\PostRepository;
use Illuminate\Support\ServiceProvider;

class DependencyProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PostInterface::class,
            PostRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
