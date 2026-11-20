<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Response hardening and request integrity helpers.
 */
final class Security
{
    private static string $nonce = '';

    public static function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups');

        $nonce = self::nonce();
        $policy = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "img-src 'self' data:",
            "font-src 'self'",
            "style-src 'self' 'unsafe-inline'",
            "script-src 'self' 'nonce-{$nonce}'",
            "frame-src 'self' https://www.google.com https://maps.google.com",
            "form-action 'self'",
            "connect-src 'self'",
        ]);
        header('Content-Security-Policy: ' . $policy);

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    public static function nonce(): string
    {
        if (self::$nonce === '') {
            self::$nonce = base64_encode(random_bytes(18));
        }

        return self::$nonce;
    }

    public static function hashIp(string $ip): string
    {
        return hash('sha256', $ip . '|' . config('app.name'));
    }

    public static function safePath(string $path): string
    {
        $path = str_replace(["\0", '\\'], '', $path);
        return '/' . ltrim($path, '/');
    }
}
