<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRating extends Model
{
    protected $fillable = [
        'room_id',
        'promedio',
        'total_resenas',
    ];

    protected function casts(): array
    {
        return [
            'promedio' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
