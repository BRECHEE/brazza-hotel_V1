<?php ?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Chambres &amp; suites</h2>
        <p>Gérez les catégories, descriptions, tarifs officiels et photographies publiées sur le site.</p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--gold btn--small" href="<?= e(route('admin.rooms.create')) ?>">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nouvelle catégorie
        </a>
    </div>
</div>

<div class="admin-note">
    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
    <p><strong>Tarifs et contenus.</strong> Ne publiez un tarif ou une description comme « vérifiés » qu’après confirmation officielle de l’hôtel. Les fiches marquées « démo » restent signalées sur le site.</p>
</div>

<?php if ($rooms === []): ?>
    <div class="admin-section">
        <div class="admin-empty">
            <i class="fa-solid fa-bed" aria-hidden="true"></i>
            <p>Aucune catégorie pour le moment.</p>
            <a class="btn btn--gold btn--small" href="<?= e(route('admin.rooms.create')) ?>">Créer la première catégorie</a>
        </div>
    </div>
<?php else: ?>
    <div class="admin-section">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Catégorie</th>
                        <th>Capacité</th>
                        <th>Tarif</th>
                        <th>Images</th>
                        <th>État</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td>
                                <?php if (filled($room['image_path'])): ?>
                                    <img class="table-thumb" src="<?= e(media_url($room['image_path'])) ?>" alt="" width="54" height="54" loading="lazy">
                                <?php else: ?>
                                    <span class="table-thumb" style="display:inline-block;background:#111;"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="cell-strong"><?= e($room['name']) ?></span>
                                <span class="cell-sub">/<?= e($room['slug']) ?></span>
                            </td>
                            <td><?= (int) $room['max_occupancy'] ?> pers.<?= $room['bed_type'] ? '<span class="cell-sub">' . e($room['bed_type']) . '</span>' : '' ?></td>
                            <td><?= e(price_display($room['price_per_night'] !== null ? (string) $room['price_per_night'] : null, (string) $room['currency'])) ?></td>
                            <td><?= (int) $room['image_count'] ?></td>
                            <td>
                                <?php if ((int) $room['is_active'] === 1): ?>
                                    <span class="status status--confirmed">Publiée</span>
                                <?php else: ?>
                                    <span class="status status--cancelled">Masquée</span>
                                <?php endif; ?>
                                <?php if ($room['content_status'] === 'demo'): ?>
                                    <span class="status status--new" style="margin-top:0.3rem;">Démo</span>
                                <?php else: ?>
                                    <span class="status status--confirmed" style="margin-top:0.3rem;">Vérifiée</span>
                                <?php endif; ?>
                                <?php if ((int) $room['is_featured'] === 1): ?>
                                    <span class="cell-sub">Mise en avant</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <?php if ((int) $room['is_active'] === 1): ?>
                                        <a class="table-action" href="<?= e(route('rooms.show', ['slug' => $room['slug']])) ?>" target="_blank" rel="noopener noreferrer" aria-label="Voir sur le site">
                                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a class="table-action" href="<?= e(route('admin.rooms.edit', ['id' => (int) $room['id']])) ?>" aria-label="Modifier">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    </a>
                                    <form action="<?= e(route('admin.rooms.delete', ['id' => (int) $room['id']])) ?>" method="post"
                                          data-confirm="Supprimer définitivement la catégorie « <?= e($room['name']) ?> » ? Cette action est irréversible.">
                                        <?= csrf_field() ?>
                                        <button class="table-action table-action--danger" type="submit" aria-label="Supprimer">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
