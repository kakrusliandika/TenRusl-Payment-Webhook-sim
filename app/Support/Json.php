<?php

declare(strict_types=1);

namespace App\Support;

use JsonException;

/**
 * Helper JSON aman:
 * - encode(): aktifkan JSON_THROW_ON_ERROR + unescaped unicode/slashes
 * - decode(): gunakan JSON_THROW_ON_ERROR
 * - tryDecode(), isJson(): utilitas non-throw
 */
final class Json
{
    /**
     * @throws JsonException
     */
    public static function encode(mixed $value, int $flags = 0, int $depth = 512): string
    {
        $flags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;

        return json_encode($value, $flags, $depth);
    }

    /**
     * @throws JsonException
     */
    public static function decode(string $json, bool $assoc = true, int $depth = 512): mixed
    {
        return json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR);
    }

    /**
     * @return mixed|null null jika gagal decode
     */
    public static function tryDecode(string $json, bool $assoc = true, int $depth = 512): mixed
    {
        try {
            return self::decode($json, $assoc, $depth);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Periksa cepat apakah string berformat JSON valid.
     */
    public static function isJson(string $value): bool
    {
        try {
            self::decode($value, true);

            return true;
        } catch (JsonException) {
            return false;
        }
    }
}
