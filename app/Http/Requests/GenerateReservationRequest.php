<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'check_in' => ['required', 'date', 'after:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:10'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'breakfast_included' => ['boolean'],
            'parking' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_in.after' => 'La fecha de entrada debe ser futura.',
            'check_out.after' => 'La fecha de salida debe ser posterior a la entrada.',
            'number_of_guests.min' => 'Mínimo 1 huésped requerido.',
            'room_id.exists' => 'La habitación no existe.',
            'client_id.exists' => 'El cliente no existe.',
        ];
    }
}
