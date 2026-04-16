<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'numero',
        'tipo',
        'capacidad',
        'precio_noche',
        'precio_fin_semana',
        'descripcion',
        'is_active',
        'servicios'
    ];

    protected function casts(): array
    {
        return [
            'servicios' => 'json',
            'is_active' => 'boolean',
            'precio_noche' => 'decimal:2',
            'precio_fin_semana' => 'decimal:2',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class, 'room_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'room_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'room_id');
    }

    public function rating()
    {
        return $this->hasOne(RoomRating::class, 'room_id');
    }

    public function room360Images(): HasMany
    {
        return $this->hasMany(Room360Image::class, 'room_id')->orderBy('display_order');
    }

    public function primary360Image()
    {
        return $this->room360Images()->where('is_primary', true)->first();
    }

    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function isAvailable($fecha_inicio, $fecha_fin): bool
    {
        return !$this->reservations()
            ->where('estado', '!=', 'cancelada')
            ->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                      ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin]);
            })
            ->exists();
    }
}