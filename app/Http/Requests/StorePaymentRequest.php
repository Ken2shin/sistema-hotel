<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => 'required|exists:reservations,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string|in:tarjeta_credito,tarjeta_debito,transferencia,efectivo',
            'descripcion' => 'sometimes|string|nullable',
        ];
    }
}
