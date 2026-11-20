<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Reservation
{
    /** @param array<string, mixed> $data */
    public static function create(array $data): string
    {
        $pdo = Database::connection();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $reference = 'BRZ-' . strtoupper(bin2hex(random_bytes(4)));
            $data['reference'] = $reference;

            try {
                $statement = $pdo->prepare(
                    'INSERT INTO reservations
                        (reference, room_id, guest_name, phone, email, check_in, check_out,
                         guests, message, source, ip_hash, user_agent)
                     VALUES
                        (:reference, :room_id, :guest_name, :phone, :email, :check_in, :check_out,
                         :guests, :message, :source, :ip_hash, :user_agent)'
                );
                $statement->execute($data);
                return $reference;
            } catch (\PDOException $exception) {
                if ((string) $exception->getCode() !== '23000' || $attempt === 3) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('Unable to generate a reservation reference.');
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminAll(?string $status = null): array
    {
        $sql = 'SELECT r.*, rm.name AS room_name
                FROM reservations r
                LEFT JOIN rooms rm ON rm.id = r.room_id';
        $params = [];
        if ($status !== null && in_array($status, ['new', 'contacted', 'confirmed', 'declined', 'cancelled'], true)) {
            $sql .= ' WHERE r.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY FIELD(r.status, "new", "contacted", "confirmed", "declined", "cancelled"), r.created_at DESC LIMIT 300';

        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT r.*, rm.name AS room_name, rm.slug AS room_slug
             FROM reservations r LEFT JOIN rooms rm ON rm.id = r.room_id
             WHERE r.id = :id'
        );
        $statement->execute(['id' => $id]);
        $reservation = $statement->fetch();
        return $reservation ?: null;
    }

    public static function updateStatus(int $id, string $status, ?string $notes): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE reservations SET status = :status, admin_notes = :notes WHERE id = :id'
        );
        $statement->execute(['status' => $status, 'notes' => $notes, 'id' => $id]);
    }

    /** @return array<string, int> */
    public static function stats(): array
    {
        $rows = Database::connection()->query(
            'SELECT status, COUNT(*) AS total FROM reservations GROUP BY status'
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'total' => array_sum(array_map('intval', $rows)),
            'new' => (int) ($rows['new'] ?? 0),
            'contacted' => (int) ($rows['contacted'] ?? 0),
            'confirmed' => (int) ($rows['confirmed'] ?? 0),
            'declined' => (int) ($rows['declined'] ?? 0),
            'cancelled' => (int) ($rows['cancelled'] ?? 0),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function latest(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        return Database::connection()->query(
            'SELECT r.*, rm.name AS room_name FROM reservations r
             LEFT JOIN rooms rm ON rm.id = r.room_id
             ORDER BY r.created_at DESC LIMIT ' . $limit
        )->fetchAll();
    }
}
