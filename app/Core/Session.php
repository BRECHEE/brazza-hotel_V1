<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Secure session wrapper, flash messages and CSRF token storage.
 */
final class Session
{
    private static bool $started = false;

    /** @param array<string, mixed> $config */
    public static function start(array $config): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        if (headers_sent()) {
            throw new RuntimeException('The session cannot be started after output.');
        }

        $secure = (bool) ($config['secure'] ?? false);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', (string) ($config['samesite'] ?? 'Lax'));
        ini_set('session.gc_maxlifetime', (string) ($config['lifetime'] ?? 7200));
        ini_set('session.sid_length', '48');
        ini_set('session.sid_bits_per_character', '6');

        session_name((string) ($config['name'] ?? 'brazza_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => (string) ($config['samesite'] ?? 'Lax'),
        ]);

        if (!session_start()) {
            throw new RuntimeException('Unable to start a secure session.');
        }

        // Age flash messages: data queued during the previous request becomes
        // readable for exactly one subsequent request.
        if (isset($_SESSION['_flash_new'])) {
            $_SESSION['_flash'] = $_SESSION['_flash_new'];
            unset($_SESSION['_flash_new']);
        } elseif (isset($_SESSION['_flash'])) {
            unset($_SESSION['_flash']);
        }

        self::$started = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        self::$started = false;
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_new'][$key] = $value;
    }

    public static function flashInput(array $input): void
    {
        unset($input['_token'], $input['password'], $input['password_confirm'], $input['company']);
        self::flash('_old', $input);
    }

    public static function flashed(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_new'][$key] ?? $_SESSION['_flash'][$key] ?? $default;
    }

    /** @return array<string, string> */
    public static function errors(): array
    {
        $errors = self::flashed('_errors', []);
        return is_array($errors) ? $errors : [];
    }

    public static function old(string $key, string $default = ''): string
    {
        $old = self::flashed('_old', []);
        $value = is_array($old) ? ($old[$key] ?? $default) : $default;
        return is_scalar($value) ? (string) $value : $default;
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        $expected = $_SESSION['_csrf_token'] ?? '';
        return is_string($expected) && $expected !== '' && is_string($token)
            && hash_equals($expected, $token);
    }

    public static function rotateCsrf(): void
    {
        unset($_SESSION['_csrf_token']);
        self::csrfToken();
    }
}
