<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Verificar si hay idioma en la sesión
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // 2. Verificar si hay idioma en cookie
        elseif ($request->hasCookie('locale')) {
            App::setLocale($request->cookie('locale'));
        }
        // 3. Usar idioma del navegador si es español
        else {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

            if (in_array($browserLocale, ['es', 'en', 'fr', 'de', 'it', 'pt'])) {
                App::setLocale($browserLocale);
            } else {
                // 4. Idioma por defecto español
                App::setLocale('es');
            }
        }

        // Configurar Carbon para fechas en español
        if (App::getLocale() == 'es') {
            \Carbon\Carbon::setLocale('es');
            setlocale(LC_TIME, 'es_ES.utf8');
        }

        return $next($request);
    }
}
