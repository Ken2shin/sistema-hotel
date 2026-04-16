<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room360Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'filename',
        'path',
        'mime_type',
        'file_size',
        'image_type',
        'horizontal_fov',
        'vertical_fov',
        'yaw',
        'pitch',
        'roll',
        'metadata',
        'is_primary',
        'display_order',
        'status',
        'hash',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'horizontal_fov' => 'integer',
        'vertical_fov' => 'integer',
        'yaw' => 'decimal:2',
        'pitch' => 'decimal:2',
        'roll' => 'decimal:2',
        'is_primary' => 'boolean',
        'display_order' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function setPrimaryImage(): void
    {
        $this->room->room360Images()
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getImageInfoAttribute(): array
    {
        return [
            'filename' => $this->filename,
            'size' => $this->file_size,
            'type' => $this->image_type,
            'fov' => [
                'horizontal' => $this->horizontal_fov,
                'vertical' => $this->vertical_fov,
            ],
            'rotation' => [
                'yaw' => $this->yaw,
                'pitch' => $this->pitch,
                'roll' => $this->roll,
            ],
            'is_primary' => $this->is_primary,
            'url' => $this->file_url,
        ];
    }
}
