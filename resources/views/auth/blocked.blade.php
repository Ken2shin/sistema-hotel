<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Bloqueado - HotelMS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-red-900 via-red-800 to-red-900 min-h-screen flex items-center justify-center font-sans">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8 space-y-8">
            <!-- Icono de advertencia -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 115.11 2.41a6 6 0 018.367 12.48z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

            <!-- Contenido -->
            <div class="text-center space-y-4">
                <h1 class="text-2xl font-bold text-slate-900">Acceso Temporalmente Bloqueado</h1>
                <p class="text-slate-600">Por razones de seguridad, tu acceso ha sido bloqueado después de múltiples intentos fallidos de inicio de sesión.</p>
            </div>

            <!-- Información de seguridad -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-3">
                <p class="text-amber-900 text-sm font-semibold flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>¿Qué sucedió?</span>
                </p>
                <ul class="text-amber-800 text-sm space-y-1">
                    <li>• Se detectaron múltiples intentos fallidos de login</li>
                    <li>• Tu acceso ha sido bloqueado por 1 minuto</li>
                    <li>• Los administradores han sido notificados</li>
                </ul>
            </div>

            <!-- Instrucciones -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-blue-900 font-semibold text-sm mb-2">¿Qué hacer ahora?</h3>
                <ol class="text-blue-800 text-sm space-y-2">
                    <li class="flex space-x-2">
                        <span class="font-bold flex-shrink-0">1.</span>
                        <span>Espera 1 minuto antes de intentar nuevamente</span>
                    </li>
                    <li class="flex space-x-2">
                        <span class="font-bold flex-shrink-0">2.</span>
                        <span>Verifica que tu contraseña sea correcta</span>
                    </li>
                    <li class="flex space-x-2">
                        <span class="font-bold flex-shrink-0">3.</span>
                        <span>Si olvidaste tu contraseña, contacta al administrador</span>
                    </li>
                </ol>
            </div>

            <!-- Botones -->
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 text-center transition">
                    Volver al Login
                </a>
                <a href="mailto:admin@hotel.com" class="block w-full border border-slate-300 text-slate-700 font-semibold py-3 rounded-lg hover:bg-slate-50 text-center transition">
                    Contactar Soporte
                </a>
            </div>

            <!-- Advertencia de seguridad -->
            <div class="text-center">
                <p class="text-slate-500 text-xs">
                    Este incidente ha sido registrado y reportado a los administradores de seguridad del sistema.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
