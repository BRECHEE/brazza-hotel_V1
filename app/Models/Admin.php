<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Admin
{
    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM admins WHERE email = :email AND is_active = 1 LIMIT 1'
        );
        $statement->execute(['email' => mb_strtolower(trim($email))]);
        $admin = $statement->fetch();
        return $admin ?: null;
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, role, is_active, last_login_at, created_at
             FROM admins WHERE id = :id AND is_active = 1'
        );
        $statement->execute(['id' => $id]);
        $admin = $statement->fetch();
        return $admin ?: null;
    }

    /** @return array<string, mixed>|null */
    public static function findCredentials(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM admins WHERE id = :id AND is_active = 1'
        );
        $statement->execute(['id' => $id]);
        $admin = $statement->fetch();
        return $admin ?: null;
    }

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM admins WHERE email = :email';
        $params = ['email' => mb_strtolower(trim($email))];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }
        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public static function touchLogin(int $id): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE admins SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    public static function updatePassword(int $id, string $password): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE admins SET password_hash = :hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $statement->execute([
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    public static function updateHash(int $id, string $hash): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE admins SET password_hash = :hash WHERE id = :id'
        );
        $statement->execute(['hash' => $hash, 'id' => $id]);
    }

    public static function create(string $name, string $email, string $password, string $role = 'manager'): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO admins (name, email, password_hash, role)
             VALUES (:name, :email, :hash, :role)'
        );
        $statement->execute([
            'name' => $name,
            'email' => mb_strtolower(trim($email)),
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function updateProfile(int $id, string $name, string $email): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE admins SET name = :name, email = :email WHERE id = :id'
        );
        $statement->execute(['name' => $name, 'email' => mb_strtolower($email), 'id' => $id]);
    }
}
