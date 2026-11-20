<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\Reservation;

final class ReservationAdminController extends AdminController
{
    private const STATUSES = ['new', 'contacted', 'confirmed', 'declined', 'cancelled'];

    public function index(): void
    {
        $status = request()->string('statut');
        if (!in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        $this->renderAdmin('admin/reservations/index', 'Demandes de réservation', [
            'reservations' => Reservation::adminAll($status),
            'activeStatus' => $status,
            'stats' => Reservation::stats(),
        ]);
    }

    public function show(string $id): void
    {
        $reservation = Reservation::find((int) $id);
        if ($reservation === null) {
            Session::flash('error', 'Cette demande est introuvable.');
            $this->redirectRoute('admin.reservations.index');
        }

        $this->renderAdmin('admin/reservations/show', 'Demande ' . $reservation['reference'], [
            'reservation' => $reservation,
        ]);
    }

    public function updateStatus(string $id): void
    {
        verify_csrf();

        $reservation = Reservation::find((int) $id);
        if ($reservation === null) {
            Session::flash('error', 'Cette demande est introuvable.');
            $this->redirectRoute('admin.reservations.index');
        }

        $status = request()->string('status');
        if (!in_array($status, self::STATUSES, true)) {
            Session::flash('error', 'Statut invalide.');
            $this->redirectRoute('admin.reservations.show', ['id' => (int) $id]);
        }

        $notes = request()->string('admin_notes');
        if (mb_strlen($notes) > 5000) {
            Session::flash('error', 'Les notes internes ne peuvent pas dépasser 5 000 caractères.');
            $this->redirectRoute('admin.reservations.show', ['id' => (int) $id]);
        }

        Reservation::updateStatus((int) $id, $status, $notes !== '' ? $notes : null);
        $this->audit('reservation.status_updated', 'reservation', (int) $id, 'Statut : ' . $status);
        Session::flash('success', 'Le statut de la demande a été mis à jour.');
        $this->redirectRoute('admin.reservations.show', ['id' => (int) $id]);
    }
}
