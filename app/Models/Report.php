<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Builder;

class Report extends Model
{
    // MassPrunable es ideal para grandes cargas: permite programar la eliminación 
    // automática de reportes temporales o fallidos para no saturar la base de datos.
    use MassPrunable;

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'frequency',
        'start_date',
        'end_date',
        'created_by',
        'status',
        'data'
    ];

    /**
     * Los atributos que deben ser convertidos (Casting).
     * En Laravel moderno se recomienda el método casts().
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            // Convierte automáticamente el JSON/JSONB de la BD a un array en PHP
            'data' => 'array', 
        ];
    }

    /**
     * Relación con el usuario que creó el reporte.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Método para marcar el reporte como exportado.
     */
    public function markAsExported(string $format, string $path): void
    {
        $this->update([
            'status' => 'exported_' . $format,
        ]);
    }

    /**
     * Configuración de Prunable para optimización de almacenamiento.
     * Ejemplo: Elimina reportes fallidos o pendientes que tengan más de 15 días.
     * (Requiere ejecutar `php artisan model:prune` en el scheduler de producción).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(15))
            ->whereIn('status', ['pending', 'failed']);
    }
}