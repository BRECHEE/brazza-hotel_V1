<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\ContactMessage;

final class MessageAdminController extends AdminController
{
    private const STATUSES = ['new', 'read', 'replied', 'archived'];

    public function index(): void
    {
        $status = request()->string('statut');
        if (!in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        $this->renderAdmin('admin/messages/index', 'Messages de contact', [
            'messages' => ContactMessage::adminAll($status),
            'activeStatus' => $status,
        ]);
    }

    public function show(string $id): void
    {
        $message = ContactMessage::find((int) $id);
        if ($message === null) {
            Session::flash('error', 'Ce message est introuvable.');
            $this->redirectRoute('admin.messages.index');
        }

        $this->renderAdmin('admin/messages/show', 'Message de ' . $message['name'], [
            'message' => $message,
        ]);
    }

    public function updateStatus(string $id): void
    {
        verify_csrf();
        $message = ContactMessage::find((int) $id);
        if ($message === null) {
            Session::flash('error', 'Ce message est introuvable.');
            $this->redirectRoute('admin.messages.index');
        }

        $status = request()->string('status');
        if (!in_array($status, self::STATUSES, true)) {
            Session::flash('error', 'Statut invalide.');
            $this->redirectRoute('admin.messages.show', ['id' => (int) $id]);
        }

        ContactMessage::updateStatus((int) $id, $status);
        $this->audit('message.status_updated', 'contact_message', (int) $id, $status);
        Session::flash('success', 'Le statut du message a été mis à jour.');
        $this->redirectRoute('admin.messages.show', ['id' => (int) $id]);
    }
}
