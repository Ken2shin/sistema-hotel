<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Encryption\Encrypter;

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

    protected $casts = [
        'is_encrypted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'value' => fn($value, $key) => $this->castValue($value),
            'is_encrypted' => 'boolean',
        ];
    }

    private function castValue($value)
    {
        if ($value === null) return null;

        $setting = $this->attributes['type'] ?? 'string';
        
        return match ($setting) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'array' => json_decode($value, true),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function getValue()
    {
        $value = $this->value;
        
        if ($this->is_encrypted && $value) {
            try {
                $value = decrypt($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $this->castValue($value);
    }

    public function setValue($value): void
    {
        $encoded = $value;

        if (is_array($value) || is_object($value)) {
            $encoded = json_encode($value);
        }

        if ($this->is_encrypted) {
            $encoded = encrypt($encoded);
        }

        $this->value = $encoded;
    }

    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    public static function setSetting($key, $value, $type = 'string', $isEncrypted = false): self
    {
        $setting = self::firstOrCreate(['key' => $key], [
            'type' => $type,
            'is_encrypted' => $isEncrypted,
        ]);

        $setting->type = $type;
        $setting->is_encrypted = $isEncrypted;
        $setting->setValue($value);
        $setting->updated_by = auth()?->id();
        $setting->save();

        return $setting;
    }
}
