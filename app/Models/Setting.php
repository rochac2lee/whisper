<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  protected $fillable = [
    'key',
    'value',
  ];

    /**
     * Obtém o valor de uma configuração.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return static::decodeValue($setting->value);
    }

    /**
     * Obtém múltiplos valores de configuração.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public static function getValues(array $keys): array
    {
        return static::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->map(fn ($value) => static::decodeValue($value))
            ->all();
    }

    /**
     * Define o valor de uma configuração.
     */
    public static function setValue(string $key, mixed $value): Setting
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => static::encodeValue($value)]
        );
    }

    /**
     * Converte valores para armazenamento textual.
     */
    public static function encodeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Converte valores armazenados para tipos PHP.
     */
    public static function decodeValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }
}
