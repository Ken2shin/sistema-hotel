<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'reservation_id' => 'required|exists:reservations,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|min:10|max:1000',
        ];
    }
}
