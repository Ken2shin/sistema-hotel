<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | Este valor es el nombre de tu aplicación. Es usado cuando el framework
    | necesita colocar el nombre en notificaciones o en la interfaz.
    | Lee directamente de tu .env: "Hotel Management System"
    |
    */

    'name' => env('APP_NAME', 'Hotel Management System'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Determina el "entorno" en el que se está ejecutando tu aplicación.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Si está en true, mostrará errores detallados con trazas de la pila.
    | En producción SIEMPRE debe estar en false.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | Esta URL es usada por la consola de Artisan para generar URLs al usar
    | la herramienta de línea de comandos.
    |
    */

    'url' => env('APP_URL', 'https://sistema-hotel-zsuu.onrender.com'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | AQUÍ ESTÁ LA SOLUCIÓN A TU BUG DE LA MEDIANOCHE.
    | Esto le dice a Laravel (y a Carbon) que use la hora de Nicaragua.
    | Ahora now()->startOfDay() será exactamente a las 00:00 hora local.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'America/Managua'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | El idioma por defecto de la aplicación. Configurado en Español para
    | que fechas, validaciones y mensajes salgan en tu idioma.
    |
    */

    'locale' => env('APP_LOCALE', 'es'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'es_ES'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Esta llave es usada por el servicio encriptador de Laravel y debe
    | ser asignada a un string aleatorio de 32 caracteres.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Estos ajustes determinan el driver usado para gestionar el modo
    | mantenimiento de Laravel.
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];