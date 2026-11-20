<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Service
{
    /** @return array<int, array<string, mixed>> */
    public static function active(int $limit = 6): array
    {
        $limit = max(1, min(12, $limit));
        return Database::connection()->query(
            'SELECT * FROM hotel_services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT ' . $limit
        )->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminAll(): array
    {
        return Database::connection()->query(
            'SELECT * FROM hotel_services ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public static function updateStatus(int $id, bool $active): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE hotel_services SET is_active = :active WHERE id = :id'
        );
        $statement->execute(['active' => $active ? 1 : 0, 'id' => $id]);
    }
}
