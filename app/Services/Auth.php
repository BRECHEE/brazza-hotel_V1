<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Admin;

/**
 * Administrator session authentication with transparent password rehashing.
 */
final class Auth
{
    private static ?array $user = null;
    private static bool $resolved = false;

    public static function attempt(string $email, string $password): bool
    {
        $admin = Admin::findByEmail($email);
        if (!$admin || !password_verify($password, (string) $admin['password_hash'])) {
            // Keep verification time reasonably uniform for unknown accounts.
            if (!$admin) {
                password_verify($password, '$2y$12$7QJ8D9mF5pE0RrGx8Qm1Ie8v7wQ6zW5p8T4y3U2iO1p0aS9dF8gH7');
            }
            return false;
        }

        if (password_needs_rehash((string) $admin['password_hash'], PASSWORD_DEFAULT)) {
            Admin::updateHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        Session::regenerate();
        Session::rotateCsrf();
        Session::put('admin_id', (int) $admin['id']);
        Session::put('admin_last_seen', time());
        Admin::touchLogin((int) $admin['id']);
        self::$user = $admin;
        self::$resolved = true;

        return true;
    }

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        if (self::$resolved) {
            return self::$user;
        }

        self::$resolved = true;
        $id = Session::get('admin_id');
        if (!is_int($id) && !ctype_digit((string) $id)) {
            return self::$user = null;
        }

        $user = Admin::find((int) $id);
        if ($user === null) {
            self::logout();
            return self::$user = null;
        }

        return self::$user = $user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user() !== null ? (int) self::user()['id'] : null;
    }

    public static function logout(): void
    {
        self::$user = null;
        self::$resolved = true;
        Session::forget('admin_id');
        Session::forget('admin_last_seen');
        Session::regenerate();
        Session::rotateCsrf();
    }

    /** @param array<string, mixed> $user */
    public static function setUser(array $user): void
    {
        self::$user = $user;
        self::$resolved = true;
    }
}
