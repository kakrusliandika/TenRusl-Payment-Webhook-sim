<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Utilitas ringan untuk menghasilkan ULID sebagai string.
 * Catatan: Untuk Eloquent Model, Laravel sudah menyediakan Concerns\HasUlids.
 * Trait ini dipakai umum (mis. untuk ID non-model / keperluan lain).
 */
trait HasUlid
{
    public static function newUlidString(): string
    {
        return (string) Str::ulid();
    }
}
