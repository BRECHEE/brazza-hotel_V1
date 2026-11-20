<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Hotel settings are small key/value records with an explicit verification
 * flag. Unverified values must never be rendered as confirmed facts.
 */
final class Setting
{
    /** @var array<string, array{value: string, is_verified: int}>|null */
    private static ?array $cache = null;

    /** @return array<string, array{value: string, is_verified: int}> */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            $rows = Database::connection()->query(
                'SELECT setting_key, setting_value, is_verified FROM hotel_settings'
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $exception) {
            error_log('[settings] ' . $exception->getMessage());
            return self::$cache = [];
        }

        self::$cache = [];
        foreach ($rows as $row) {
            self::$cache[(string) $row['setting_key']] = [
                'value' => (string) $row['setting_value'],
                'is_verified' => (int) $row['is_verified'],
            ];
        }

        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        $value = self::all()[$key]['value'] ?? '';
        return trim($value) !== '' ? $value : $default;
    }

    public static function isVerified(string $key): bool
    {
        return (int) (self::all()[$key]['is_verified'] ?? 0) === 1;
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }

    /** @param array<string, string> $values */
    public static function updateMany(array $values, array $verifiedKeys = []): void
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'UPDATE hotel_settings
             SET setting_value = :value, is_verified = :verified, updated_at = CURRENT_TIMESTAMP
             WHERE setting_key = :key'
        );

        foreach ($values as $key => $value) {
            $trimmed = trim((string) $value);
            $statement->execute([
                'value' => $trimmed,
                'verified' => ($trimmed !== '' && in_array($key, $verifiedKeys, true)) ? 1 : 0,
                'key' => $key,
            ]);
        }

        self::clearCache();
    }
}
