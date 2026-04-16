<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    // Estos traits permiten validar formularios y verificar permisos fácilmente
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Retorna una respuesta JSON de éxito estandarizada.
     * Ideal para que tus controladores API le respondan a Next.js.
     */
    protected function successResponse($data, $message = 'Operación exitosa', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Retorna una respuesta JSON de error estandarizada.
     * Útil cuando falla un pago, no hay habitaciones, o falla el login.
     */
    protected function errorResponse($message, $code = 400, $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}