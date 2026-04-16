<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:clients,email,' . $this->route('id'),
            'telefono' => 'sometimes|string|max:20',
            'cedula' => 'sometimes|string|unique:clients,cedula,' . $this->route('id'),
            'pais' => 'sometimes|string|max:100',
            'ciudad' => 'sometimes|string|max:100',
            'direccion' => 'sometimes|string|max:500',
            'tipo_cliente' => 'sometimes|string|in:regular,vip,corporativo',
            'notas' => 'sometimes|string|nullable',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
