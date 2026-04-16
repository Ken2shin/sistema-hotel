<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telefono' => 'required|string|max:20',
            'cedula' => 'required|string|unique:clients,cedula',
            'pais' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'direccion' => 'required|string|max:500',
            'tipo_cliente' => 'sometimes|string|in:regular,vip,corporativo',
            'notas' => 'sometimes|string|nullable',
        ];
    }
}
