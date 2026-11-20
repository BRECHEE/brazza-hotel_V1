<?php
$statuses = [
    'new' => 'Nouvelles',
    'contacted' => 'Contactées',
    'confirmed' => 'Confirmées',
    'declined' => 'Refusées',
    'cancelled' => 'Annulées',
];
$filterUrl = static fn (?string $status): string => route('admin.reservations.index', $status ? ['statut' => $status] : []);
?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Demandes de réservation</h2>
        <p>Chaque demande doit être vérifiée puis confirmée manuellement. Aucune disponibilité n’est automatique.</p>
    </div>
</div>

<nav class="admin-filters" aria-label="Filtrer les demandes">
    <a class="admin-filter <?= $activeStatus === null ? 'is-active' : '' ?>" href="<?= e($filterUrl(null)) ?>">
        Toutes <span><?= (int) $stats['total'] ?></span>
    </a>
    <?php foreach ($statuses as $key => $label): ?>
        <a class="admin-filter <?= $activeStatus === $key ? 'is-active' : '' ?>" href="<?= e($filterUrl($key)) ?>">
            <?= e($label) ?> <span><?= (int) ($stats[$key] ?? 0) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($reservations === []): ?>
    <div class="admin-section">
        <div class="admin-empty">
            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
            <p>Aucune demande ne correspond à ce filtre.</p>
        </div>
    </div>
<?php else: ?>
    <div class="admin-section">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Voyageur</th>
                        <th>Séjour</th>
                        <th>Chambre</th>
                        <th>Reçue le</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><span class="cell-mono"><?= e($reservation['reference']) ?></span></td>
                            <td>
                                <span class="cell-strong"><?= e($reservation['guest_name']) ?></span>
                                <span class="cell-sub"><?= e($reservation['phone']) ?></span>
                            </td>
                            <td>
                                <span class="cell-strong"><?= e(format_date_fr($reservation['check_in'])) ?></span>
                                <span class="cell-sub">au <?= e(format_date_fr($reservation['check_out'])) ?> · <?= (int) $reservation['guests'] ?> pers.</span>
                            </td>
                            <td><?= e($reservation['room_name'] ?: '—') ?></td>
                            <td>
                                <span class="cell-strong"><?= e(format_date_fr(substr((string) $reservation['created_at'], 0, 10))) ?></span>
                                <span class="cell-sub"><?= e(substr((string) $reservation['created_at'], 11, 5)) ?></span>
                            </td>
                            <td><span class="<?= e(reservation_status_class((string) $reservation['status'])) ?>"><?= e(status_label((string) $reservation['status'])) ?></span></td>
                            <td>
                                <a class="table-action" href="<?= e(route('admin.reservations.show', ['id' => (int) $reservation['id']])) ?>" aria-label="Ouvrir la demande <?= e($reservation['reference']) ?>">
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
