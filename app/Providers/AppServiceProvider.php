<?php

namespace App\Providers;

use App\Models\actividades;
use App\Observers\ActividadObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        actividades::observe(ActividadObserver::class);
    }
}
