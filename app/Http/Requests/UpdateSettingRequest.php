<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required'],
            'type' => ['required', 'in:string,boolean,integer,decimal,array,json'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_encrypted' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'La clave de configuración es requerida.',
            'value.required' => 'El valor es requerido.',
            'type.in' => 'Tipo de dato no válido.',
        ];
    }
}
