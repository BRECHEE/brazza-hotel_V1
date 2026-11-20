<?php
$firstName = explode(' ', trim((string) ($adminUser['name'] ?? 'Administrateur')))[0];
$actionLabels = [
    'reservation.status_updated' => 'Statut de réservation mis à jour',
    'room.created' => 'Catégorie de chambre créée',
    'room.updated' => 'Catégorie de chambre modifiée',
    'room.deleted' => 'Catégorie de chambre supprimée',
    'room.images_uploaded' => 'Photographies ajoutées',
    'room.image_deleted' => 'Photographie supprimée',
    'gallery.image_uploaded' => 'Image ajoutée à la galerie',
    'gallery.image_deleted' => 'Image retirée de la galerie',
    'gallery.category_created' => 'Catégorie de galerie créée',
    'message.status_updated' => 'Statut d’un message modifié',
    'settings.updated' => 'Informations du site mises à jour',
    'admin.profile_updated' => 'Profil administrateur modifié',
    'admin.password_updated' => 'Mot de passe administrateur modifié',
];
?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Bonjour, <?= e($firstName) ?>.</h2>
        <p>Voici l’activité récente de Brazza Hotel. Les demandes restent à confirmer manuellement.</p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--ghost btn--small" href="<?= e(route('admin.rooms.create')) ?>">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nouvelle chambre
        </a>
        <a class="btn btn--gold btn--small" href="<?= e(route('admin.reservations.index', ['statut' => 'new'])) ?>">
            Voir les demandes <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</div>

<div class="stats-grid">
    <a class="stat-card stat-card--accent" href="<?= e(route('admin.reservations.index', ['statut' => 'new'])) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-bell" aria-hidden="true"></i> Nouvelles demandes</span>
        <strong class="stat-card__value"><?= (int) $stats['reservations_new'] ?></strong>
        <span class="stat-card__hint">À traiter par l’équipe</span>
    </a>
    <a class="stat-card" href="<?= e(route('admin.reservations.index')) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Demandes totales</span>
        <strong class="stat-card__value"><?= (int) $stats['reservations_total'] ?></strong>
        <span class="stat-card__hint">Toutes périodes confondues</span>
    </a>
    <a class="stat-card" href="<?= e(route('admin.reservations.index', ['statut' => 'confirmed'])) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Confirmées</span>
        <strong class="stat-card__value"><?= (int) $stats['reservations_confirmed'] ?></strong>
        <span class="stat-card__hint">Après validation de l’hôtel</span>
    </a>
    <a class="stat-card" href="<?= e(route('admin.messages.index', ['statut' => 'new'])) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-envelope" aria-hidden="true"></i> Messages non lus</span>
        <strong class="stat-card__value"><?= (int) $stats['messages_new'] ?></strong>
        <span class="stat-card__hint">Formulaires de contact</span>
    </a>
    <a class="stat-card" href="<?= e(route('admin.rooms.index')) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-bed" aria-hidden="true"></i> Catégories</span>
        <strong class="stat-card__value"><?= (int) $stats['rooms_total'] ?></strong>
        <span class="stat-card__hint">Chambres &amp; suites</span>
    </a>
    <a class="stat-card" href="<?= e(route('admin.gallery.index')) ?>">
        <span class="stat-card__label"><i class="fa-solid fa-images" aria-hidden="true"></i> Images</span>
        <strong class="stat-card__value"><?= (int) $stats['gallery_total'] ?></strong>
        <span class="stat-card__hint">Galerie du site</span>
    </a>
</div>

<div class="admin-grid admin-grid--sidebar">
    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <div>
                    <h2>Dernières demandes</h2>
                    <p>Les disponibilités ne sont jamais confirmées automatiquement.</p>
                </div>
                <a href="<?= e(route('admin.reservations.index')) ?>">Tout voir</a>
            </div>
            <?php if ($reservations === []): ?>
                <div class="admin-empty">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <p>Aucune demande pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Voyageur</th>
                                <th>Séjour</th>
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
                                        <span class="cell-sub"><?= e($reservation['room_name'] ?: 'Catégorie supprimée') ?></span>
                                    </td>
                                    <td>
                                        <span class="cell-strong"><?= e(format_date_fr($reservation['check_in'], false)) ?> → <?= e(format_date_fr($reservation['check_out'], false)) ?></span>
                                        <span class="cell-sub"><?= (int) $reservation['guests'] ?> voyageur(s)</span>
                                    </td>
                                    <td><span class="<?= e(reservation_status_class((string) $reservation['status'])) ?>"><?= e(status_label((string) $reservation['status'])) ?></span></td>
                                    <td>
                                        <a class="table-action" href="<?= e(route('admin.reservations.show', ['id' => (int) $reservation['id']])) ?>" aria-label="Voir la demande">
                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <div>
                    <h2>Messages récents</h2>
                    <p>Demandes reçues via le formulaire de contact.</p>
                </div>
                <a href="<?= e(route('admin.messages.index')) ?>">Tout voir</a>
            </div>
            <?php if ($messages === []): ?>
                <div class="admin-empty">
                    <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                    <p>Aucun nouveau message.</p>
                </div>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Expéditeur</th><th>Sujet</th><th>Reçu</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($messages, 0, 5) as $message): ?>
                                <tr>
                                    <td>
                                        <span class="cell-strong"><?= e($message['name']) ?></span>
                                        <span class="cell-sub"><?= e($message['email']) ?></span>
                                    </td>
                                    <td><?= e($message['subject']) ?></td>
                                    <td><?= e(format_date_fr(substr((string) $message['created_at'], 0, 10))) ?></td>
                                    <td>
                                        <a class="table-action" href="<?= e(route('admin.messages.show', ['id' => (int) $message['id']])) ?>" aria-label="Lire le message">
                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <div>
                    <h2>Activité</h2>
                    <p>Journal des dernières actions.</p>
                </div>
            </div>
            <div class="admin-section__body">
                <?php if ($activity === []): ?>
                    <p class="admin-muted">Aucune activité enregistrée.</p>
                <?php else: ?>
                    <div class="admin-activity">
                        <?php foreach ($activity as $entry): ?>
                            <div class="activity-item">
                                <span class="activity-dot" aria-hidden="true"></span>
                                <div>
                                    <p><?= e($actionLabels[(string) $entry['action']] ?? ucfirst(str_replace(['.', '_'], [' · ', ' '], (string) $entry['action']))) ?></p>
                                    <small><?= e($entry['admin_name'] ?: 'Système') ?> · <?= e(format_datetime_fr((string) $entry['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <div>
                    <h2>Accès rapides</h2>
                    <p>Les actions les plus courantes.</p>
                </div>
            </div>
            <div class="admin-section__body">
                <div class="admin-grid" style="gap: 0.45rem;">
                    <a class="btn btn--ghost btn--small" href="<?= e(route('admin.rooms.create')) ?>">Ajouter une catégorie</a>
                    <a class="btn btn--ghost btn--small" href="<?= e(route('admin.gallery.index')) ?>">Gérer la galerie</a>
                    <a class="btn btn--ghost btn--small" href="<?= e(route('admin.settings.index')) ?>">Compléter les informations</a>
                    <a class="btn btn--ghost btn--small" href="<?= e(route('admin.profile.index')) ?>">Sécuriser mon compte</a>
                </div>
            </div>
        </section>
    </div>
</div>
