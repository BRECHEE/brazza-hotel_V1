<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Gallery
{
    /** @return array<int, array<string, mixed>> */
    public static function categories(): array
    {
        return Database::connection()->query(
            'SELECT c.*, COUNT(i.id) AS image_count
             FROM gallery_categories c
             LEFT JOIN gallery_images i ON i.category_id = c.id AND i.is_active = 1
             GROUP BY c.id, c.slug, c.name, c.sort_order, c.created_at
             ORDER BY c.sort_order ASC, c.name ASC'
        )->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    public static function images(?string $categorySlug = null, bool $featuredOnly = false, int $limit = 0): array
    {
        $sql = 'SELECT i.*, c.name AS category_name, c.slug AS category_slug
                FROM gallery_images i
                LEFT JOIN gallery_categories c ON c.id = i.category_id
                WHERE i.is_active = 1';
        $params = [];

        if ($categorySlug !== null && $categorySlug !== '') {
            $sql .= ' AND c.slug = :category';
            $params['category'] = $categorySlug;
        }
        if ($featuredOnly) {
            $sql .= ' AND i.is_featured = 1';
        }

        $sql .= ' ORDER BY i.sort_order ASC, i.id ASC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . max(1, min(30, $limit));
        }

        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    public static function featured(int $limit = 6): array
    {
        return self::images(null, true, $limit);
    }

    public static function countActive(?string $categorySlug = null): int
    {
        $sql = 'SELECT COUNT(*) FROM gallery_images i
                LEFT JOIN gallery_categories c ON c.id = i.category_id
                WHERE i.is_active = 1';
        $params = [];
        if ($categorySlug !== null && $categorySlug !== '') {
            $sql .= ' AND c.slug = :category';
            $params['category'] = $categorySlug;
        }
        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn();
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminAll(): array
    {
        return Database::connection()->query(
            'SELECT i.*, c.name AS category_name
             FROM gallery_images i
             LEFT JOIN gallery_categories c ON c.id = i.category_id
             ORDER BY i.sort_order ASC, i.id DESC'
        )->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM gallery_images WHERE id = :id');
        $statement->execute(['id' => $id]);
        $image = $statement->fetch();
        return $image ?: null;
    }

    public static function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO gallery_images
                (category_id, file_path, alt_text, caption, is_featured, is_active, content_status, sort_order)
             VALUES
                (:category_id, :file_path, :alt_text, :caption, :is_featured, :is_active, :content_status, :sort_order)'
        );
        $statement->execute($data);
        return (int) Database::connection()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM gallery_images WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public static function categorySlugExists(string $slug): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM gallery_categories WHERE slug = :slug'
        );
        $statement->execute(['slug' => $slug]);
        return (int) $statement->fetchColumn() > 0;
    }

    public static function createCategory(string $name, string $slug): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO gallery_categories (name, slug, sort_order)
             VALUES (:name, :slug, COALESCE((SELECT MAX(sort_order) + 10 FROM gallery_categories), 10))'
        );
        $statement->execute(['name' => $name, 'slug' => $slug]);
        return (int) Database::connection()->lastInsertId();
    }
}
