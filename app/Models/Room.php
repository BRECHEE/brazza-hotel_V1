<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Room
{
    /** @return array<int, array<string, mixed>> */
    public static function featured(int $limit = 3): array
    {
        return self::publicQuery(true, $limit);
    }

    /** @return array<int, array<string, mixed>> */
    public static function allActive(): array
    {
        return self::publicQuery(false);
    }

    /** @return array<int, array<string, mixed>> */
    private static function publicQuery(bool $featuredOnly, int $limit = 0): array
    {
        $limit = max(0, min(24, $limit));
        $sql = "SELECT r.*,
                    (SELECT ri.file_path FROM room_images ri
                     WHERE ri.room_id = r.id
                     ORDER BY ri.is_primary DESC, ri.sort_order ASC LIMIT 1) AS image_path,
                    (SELECT ri.alt_text FROM room_images ri
                     WHERE ri.room_id = r.id
                     ORDER BY ri.is_primary DESC, ri.sort_order ASC LIMIT 1) AS image_alt
                FROM rooms r
                WHERE r.is_active = 1";

        if ($featuredOnly) {
            $sql .= ' AND r.is_featured = 1';
        }

        $sql .= ' ORDER BY r.sort_order ASC, r.id ASC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }

        return Database::connection()->query($sql)->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM rooms WHERE slug = :slug AND is_active = 1 LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);
        $room = $statement->fetch();

        return $room ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function images(int $roomId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM room_images WHERE room_id = :room_id ORDER BY is_primary DESC, sort_order ASC, id ASC'
        );
        $statement->execute(['room_id' => $roomId]);
        return $statement->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    public static function amenities(int $roomId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT a.* FROM amenities a
             INNER JOIN room_amenities ra ON ra.amenity_id = a.id
             WHERE ra.room_id = :room_id
             ORDER BY a.sort_order ASC, a.name ASC'
        );
        $statement->execute(['room_id' => $roomId]);
        return $statement->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function findActive(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM rooms WHERE id = :id AND is_active = 1 LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $room = $statement->fetch();
        return $room ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function options(): array
    {
        return Database::connection()->query(
            'SELECT id, name, max_occupancy FROM rooms WHERE is_active = 1 ORDER BY sort_order, name'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------------------
    // Administration
    // ---------------------------------------------------------------------

    /** @return array<int, array<string, mixed>> */
    public static function adminAll(): array
    {
        return Database::connection()->query(
            'SELECT r.*,
                (SELECT COUNT(*) FROM room_images ri WHERE ri.room_id = r.id) AS image_count,
                (SELECT ri.file_path FROM room_images ri WHERE ri.room_id = r.id
                 ORDER BY ri.is_primary DESC, ri.sort_order ASC LIMIT 1) AS image_path
             FROM rooms r
             ORDER BY r.sort_order ASC, r.id ASC'
        )->fetchAll();
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM rooms WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }
        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    /** @return array<string, mixed>|null */
    public static function adminFind(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM rooms WHERE id = :id');
        $statement->execute(['id' => $id]);
        $room = $statement->fetch();
        return $room ?: null;
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO rooms
                (slug, name, tagline, short_description, description, price_per_night, currency,
                 max_occupancy, bed_type, size_sqm, is_featured, is_active, content_status,
                 sort_order, meta_title, meta_description)
             VALUES
                (:slug, :name, :tagline, :short_description, :description, :price, :currency,
                 :max_occupancy, :bed_type, :size_sqm, :is_featured, :is_active, :content_status,
                 :sort_order, :meta_title, :meta_description)'
        );
        $statement->execute($data);
        return (int) Database::connection()->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE rooms SET
                slug = :slug, name = :name, tagline = :tagline,
                short_description = :short_description, description = :description,
                price_per_night = :price, currency = :currency, max_occupancy = :max_occupancy,
                bed_type = :bed_type, size_sqm = :size_sqm, is_featured = :is_featured,
                is_active = :is_active, content_status = :content_status,
                sort_order = :sort_order, meta_title = :meta_title, meta_description = :meta_description
             WHERE id = :id'
        );
        $statement->execute($data);
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM rooms WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public static function addImage(int $roomId, string $path, string $alt, bool $primary): void
    {
        $pdo = Database::connection();
        if ($primary) {
            $reset = $pdo->prepare('UPDATE room_images SET is_primary = 0 WHERE room_id = :room_id');
            $reset->execute(['room_id' => $roomId]);
        }
        $maxStatement = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) FROM room_images WHERE room_id = :room_id');
        $maxStatement->execute(['room_id' => $roomId]);
        $sortOrder = (int) $maxStatement->fetchColumn() + 10;

        $statement = $pdo->prepare(
            'INSERT INTO room_images (room_id, file_path, alt_text, is_primary, sort_order)
             VALUES (:room_id, :path, :alt, :primary, :sort_order)'
        );
        $statement->execute([
            'room_id' => $roomId,
            'path' => $path,
            'alt' => $alt,
            'primary' => $primary ? 1 : 0,
            'sort_order' => $sortOrder,
        ]);
    }

    /** @return array<string, mixed>|null */
    public static function findImage(int $imageId): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM room_images WHERE id = :id');
        $statement->execute(['id' => $imageId]);
        $image = $statement->fetch();
        return $image ?: null;
    }

    public static function deleteImage(int $imageId): void
    {
        $statement = Database::connection()->prepare('DELETE FROM room_images WHERE id = :id');
        $statement->execute(['id' => $imageId]);
    }
}
