<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store360ImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'image' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:52428800',
            ],
            'image_type' => ['required', 'in:equirectangular,cubemap,spherical,standard'],
            'horizontal_fov' => ['integer', 'between:1,360'],
            'vertical_fov' => ['integer', 'between:1,180'],
            'yaw' => ['numeric', 'between:-180,180'],
            'pitch' => ['numeric', 'between:-90,90'],
            'roll' => ['numeric', 'between:-180,180'],
            'is_primary' => ['boolean'],
            'metadata' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'La imagen es requerida.',
            'image.mimes' => 'Solo se aceptan archivos JPG, PNG o WebP.',
            'image.max' => 'La imagen no puede exceder 50MB.',
            'room_id.exists' => 'La habitación no existe.',
            'image_type.in' => 'Tipo de imagen 360° no válido.',
        ];
    }
}
