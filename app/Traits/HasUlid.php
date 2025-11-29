<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait untuk primary key berbasis ULID pada Eloquent Model.
 *
 * - Menyetel $incrementing=false dan $keyType='string'
 * - Mengisi kolom kunci (getKeyName()) dengan ULID saat creating jika kosong
 */
trait HasUlid
{
    /**
     * Inisialisasi properti model saat trait dipakai.
     */
    public function initializeHasUlid(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    /**
     * Hook Eloquent: isi kunci ULID saat creating jika belum ada.
     */
    public static function bootHasUlid(): void
    {
        static::creating(function ($model): void {
            $key = $model->getKeyName();
            if (! isset($model->{$key}) || $model->{$key} === '') {
                // Str::ulid() membutuhkan symfony/uid; cast ke string untuk penyimpanan
                $model->{$key} = strtolower((string) Str::ulid());
            }
        });
    }

    /**
     * Helper untuk memperoleh nilai ULID dari model.
     */
    public function getUlid(): string
    {
        return (string) $this->getKey();
    }
}
