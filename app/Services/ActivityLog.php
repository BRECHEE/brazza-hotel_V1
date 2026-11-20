<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Security;

final class ActivityLog
{
        /** @return array<int, array<string, mixed>> */
    public static function recent(int $limit = 8): array
    {
        $limit = max(1, min(50, $limit));
        return Database::connection()->query(
            'SELECT l.*, a.name AS admin_name
             FROM admin_activity_log l
             LEFT JOIN admins a ON a.id = l.admin_id
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT ' . $limit
        )->fetchAll();
    }

    public static function record(string $action, ?string $targetType = null, ?int $targetId = null, ?string $context = null): void
    {
        try {
            $statement = Database::connection()->prepare(
                'INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, context, ip_hash)
                 VALUES (:admin_id, :action, :target_type, :target_id, :context, :ip_hash)'
            );
            $statement->execute([
                'admin_id' => Auth::id(),
                'action' => substr($action, 0, 80),
                'target_type' => $targetType,
                'target_id' => $targetId,
                'context' => $context !== null ? mb_substr($context, 0, 255) : null,
                'ip_hash' => Security::hashIp(request()->ip),
            ]);
        } catch (\Throwable $exception) {
            error_log('[activity] ' . $exception->getMessage());
        }
    }
}
