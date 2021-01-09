<?php

namespace MarcNewton\LaravelASAL;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use MarcNewton\LaravelASAL\Http\Middleware\LocaleMiddleware;

class AcceptLanguageServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/language.php', 'language');
    }

    /**
     * Bootstrap any package services.
     *
     */
    public function boot()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(LocaleMiddleware::class);
    }
}
