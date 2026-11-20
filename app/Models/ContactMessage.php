<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ContactMessage
{
    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO contact_messages (name, email, phone, subject, message, ip_hash)
             VALUES (:name, :email, :phone, :subject, :message, :ip_hash)'
        );
        $statement->execute($data);
        return (int) Database::connection()->lastInsertId();
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminAll(?string $status = null): array
    {
        $sql = 'SELECT * FROM contact_messages';
        $params = [];
        if ($status !== null && in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
            $sql .= ' WHERE status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY FIELD(status, "new", "read", "replied", "archived"), created_at DESC LIMIT 300';

        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM contact_messages WHERE id = :id');
        $statement->execute(['id' => $id]);
        $message = $statement->fetch();
        return $message ?: null;
    }

    public static function updateStatus(int $id, string $status): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE contact_messages SET status = :status WHERE id = :id'
        );
        $statement->execute(['status' => $status, 'id' => $id]);
    }

    public static function unreadCount(): int
    {
        return (int) Database::connection()->query(
            "SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"
        )->fetchColumn();
    }
}
