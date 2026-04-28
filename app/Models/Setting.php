<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_encrypted',
        'updated_by',
    ];

    /**
     * Eliminamos la doble declaración de casts y quitamos 'value'.
     * 'value' se manejará manualmente para evitar crashes.
     */
    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Lógica segura para extraer y castear el valor.
     */
    public function getValue()
    {
        $val = $this->attributes['value'] ?? null;
        
        if ($val === null) return null;

        if ($this->is_encrypted) {
            try {
                $val = decrypt($val);
            } catch (\Exception $e) {
                return '[ERROR DE DESENCRIPTACIÓN]';
            }
        }

        return match ($this->type) {
            'boolean', 'bool' => filter_var($val, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $val,
            'decimal', 'float' => (float) $val,
            'array', 'json' => json_decode($val, true) ?? [],
            default => (string) $val,
        };
    }

    /**
     * Lógica segura para empaquetar y encriptar antes de guardar.
     */
    public function setValue($value): void
    {
        if (in_array($this->type, ['array', 'json']) && (is_array($value) || is_object($value))) {
            $value = json_encode($value);
        }

        if ($this->type === 'boolean' || $this->type === 'bool') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        }

        if ($this->is_encrypted) {
            $value = encrypt((string) $value);
        }

        $this->attributes['value'] = (string) $value;
    }

    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Modificado para actualizar la descripción en la misma consulta
     * evitando doble impacto en la base de datos.
     */
    public static function setSetting($key, $value, $type = 'string', $description = null, $isEncrypted = false): self
    {
        $setting = self::firstOrNew(['key' => $key]);

        $setting->type = $type;
        $setting->is_encrypted = $isEncrypted;
        
        if ($description !== null) {
            $setting->description = $description;
        }
        
        $setting->setValue($value);
        $setting->updated_by = auth()?->id();
        $setting->save();

        return $setting;
    }
}