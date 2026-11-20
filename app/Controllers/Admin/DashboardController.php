<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\ContactMessage;
use App\Models\Reservation;
use App\Services\ActivityLog;

final class DashboardController extends AdminController
{
    public function index(): void
    {
        $pdo = Database::connection();
        $reservationStats = Reservation::stats();

        $this->renderAdmin('admin/dashboard/index', 'Tableau de bord', [
            'stats' => [
                'reservations_total' => $reservationStats['total'],
                'reservations_new' => $reservationStats['new'],
                'reservations_confirmed' => $reservationStats['confirmed'],
                'messages_new' => ContactMessage::unreadCount(),
                'rooms_total' => (int) $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn(),
                'gallery_total' => (int) $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn(),
            ],
            'reservations' => Reservation::latest(6),
            'messages' => ContactMessage::adminAll('new'),
            'activity' => ActivityLog::recent(8),
        ]);
    }
}
