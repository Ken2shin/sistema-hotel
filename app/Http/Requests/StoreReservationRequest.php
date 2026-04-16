<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'room_id' => 'required|exists:rooms,id',
            'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
            'fecha_fin' => 'required|date_format:Y-m-d H:i:s|after:fecha_inicio',
            'notas' => 'sometimes|string|nullable',
        ];
    }
}
