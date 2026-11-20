<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

/**
 * Session-based throttle for public forms and admin login.
 *
 * This is intentionally simple and protects against casual abuse on a shared
 * hosting setup. Production deployments should add an edge/WAF rate limit too.
 */
final class RateLimiter
{
    public static function tooMany(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $bucket = self::bucket($key, $decaySeconds);
        return count($bucket) >= $maxAttempts;
    }

    public static function hit(string $key, int $decaySeconds): void
    {
        $bucket = self::bucket($key, $decaySeconds);
        $bucket[] = time();
        $bucket = array_slice($bucket, -30);
        Session::put('_throttle_' . $key, $bucket);
    }

    public static function clear(string $key): void
    {
        Session::forget('_throttle_' . $key);
    }

    /** @return array<int, int> */
    private static function bucket(string $key, int $decaySeconds): array
    {
        $key = preg_replace('/[^a-z0-9_]/', '', strtolower($key)) ?? 'default';
        $value = Session::get('_throttle_' . $key, []);
        $cutoff = time() - $decaySeconds;
        $bucket = array_values(array_filter(
            is_array($value) ? $value : [],
            static fn ($timestamp): bool => is_int($timestamp) && $timestamp >= $cutoff
        ));
        Session::put('_throttle_' . $key, $bucket);
        return $bucket;
    }
}
