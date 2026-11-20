<?php
$statuses = ['new' => 'Nouveaux', 'read' => 'Lus', 'replied' => 'Répondus', 'archived' => 'Archivés'];
$filterUrl = static fn (?string $status): string => route('admin.messages.index', $status ? ['statut' => $status] : []);
?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Messages de contact</h2>
        <p>Les messages reçus via le formulaire public. Aucun envoi automatique d’e-mail n’est configuré.</p>
    </div>
</div>

<nav class="admin-filters" aria-label="Filtrer les messages">
    <a class="admin-filter <?= $activeStatus === null ? 'is-active' : '' ?>" href="<?= e($filterUrl(null)) ?>">Tous</a>
    <?php foreach ($statuses as $key => $label): ?>
        <a class="admin-filter <?= $activeStatus === $key ? 'is-active' : '' ?>" href="<?= e($filterUrl($key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($messages === []): ?>
    <div class="admin-section">
        <div class="admin-empty">
            <i class="fa-regular fa-envelope" aria-hidden="true"></i>
            <p>Aucun message ne correspond à ce filtre.</p>
        </div>
    </div>
<?php else: ?>
    <div class="admin-section">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>Expéditeur</th><th>Sujet</th><th>Contact</th><th>Reçu</th><th>Statut</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><span class="cell-strong"><?= e($message['name']) ?></span></td>
                            <td><?= e($message['subject']) ?></td>
                            <td>
                                <span class="cell-sub"><?= e($message['email']) ?></span>
                                <?php if (filled($message['phone'])): ?><span class="cell-sub"><?= e($message['phone']) ?></span><?php endif; ?>
                            </td>
                            <td>
                                <span class="cell-strong"><?= e(format_date_fr(substr((string) $message['created_at'], 0, 10))) ?></span>
                                <span class="cell-sub"><?= e(substr((string) $message['created_at'], 11, 5)) ?></span>
                            </td>
                            <td><span class="<?= e(reservation_status_class((string) $message['status'])) ?>"><?= e(status_label((string) $message['status'])) ?></span></td>
                            <td>
                                <a class="table-action" href="<?= e(route('admin.messages.show', ['id' => (int) $message['id']])) ?>" aria-label="Lire le message">
                                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
