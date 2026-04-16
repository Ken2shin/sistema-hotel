<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:revenue,occupancy,clients,rooms,payment_methods'],
            'frequency' => ['required', 'in:daily,weekly,monthly,custom'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date'],
            'filters' => ['nullable', 'json'],
            'format' => ['required', 'in:pdf,csv,xlsx,json'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Tipo de reporte no válido.',
            'format.in' => 'Formato de exportación no válido.',
            'start_date.before' => 'La fecha inicio debe ser anterior a la fecha fin.',
        ];
    }
}
