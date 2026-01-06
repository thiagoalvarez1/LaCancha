<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Prevenir lazy loading en producción
        Model::preventLazyLoading(!app()->isProduction());

        // Configuración de la base de datos
        Schema::defaultStringLength(191);

        // Configurar Carbon para español globalmente
        Carbon::setLocale('es');

        // Para Laravel 9+ también configurar
        if (class_exists('Illuminate\Support\Facades\Date')) {
            \Illuminate\Support\Facades\Date::setLocale('es');
        }

        // Configurar el locale para fechas SOLAMENTE si está disponible
        if (function_exists('setlocale') && defined('LC_TIME')) {
            try {
                setlocale(LC_TIME, 'es_ES.utf8');
            } catch (\Exception $e) {
                // Ignorar error si falla
            }
        }

        // EVITAR LC_MONETARY y LC_NUMERIC que causan problemas
        // No uses: setlocale(LC_MONETARY, 'es_ES.utf8');
        // No uses: setlocale(LC_NUMERIC, 'es_ES.utf8');

        // Forzar el idioma en toda la aplicación si es necesario
        app()->setLocale('es');

        // Compartir el idioma actual con todas las vistas (opcional)
        \Illuminate\Support\Facades\View::share('currentLocale', app()->getLocale());
    }
}
