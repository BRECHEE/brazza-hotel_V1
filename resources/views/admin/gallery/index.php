<?php ?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Galerie du site</h2>
        <p>Gérez les catégories et les photographies affichées dans la galerie publique.</p>
    </div>
</div>

<div class="admin-note">
    <i class="fa-solid fa-camera" aria-hidden="true"></i>
    <p><strong>Visuels d’illustration.</strong> Les images actuellement en ligne sont des illustrations non contractuelles. Remplacez-les progressivement par les photographies officielles de l’hôtel.</p>
</div>

<div class="admin-grid admin-grid--2">
    <section class="admin-form-card">
        <div class="admin-form-card__head">
            <h2>Ajouter une image</h2>
        </div>
        <div class="admin-form-card__body">
            <form action="<?= e(route('admin.gallery.store')) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="field <?= has_error('image') ? 'has-error' : '' ?>">
                    <label for="gallery-image">Fichier image <span class="required">*</span></label>
                    <input type="file" id="gallery-image" name="image" accept="image/jpeg,image/png,image/webp" required data-image-input="#gallery-file-list">
                    <span class="file-list" id="gallery-file-list">Aucun fichier sélectionné</span>
                    <?php if (has_error('image')): ?><span class="field__error"><?= error_for('image') ?></span><?php endif; ?>
                </div>
                <div class="field" style="margin-top:0.8rem;">
                    <label for="gallery-category">Catégorie <span class="required">*</span></label>
                    <select id="gallery-category" name="category_id" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= (int) old('category_id') === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (has_error('category_id')): ?><span class="field__error"><?= error_for('category_id') ?></span><?php endif; ?>
                </div>
                <div class="field" style="margin-top:0.8rem;">
                    <label for="gallery-alt">Texte alternatif <span class="required">*</span></label>
                    <input type="text" id="gallery-alt" name="alt_text" value="<?= old('alt_text') ?>" required maxlength="180" placeholder="Décrire l’image pour l’accessibilité">
                    <?php if (has_error('alt_text')): ?><span class="field__error"><?= error_for('alt_text') ?></span><?php endif; ?>
                </div>
                <div class="field" style="margin-top:0.8rem;">
                    <label for="gallery-caption">Légende publique</label>
                    <input type="text" id="gallery-caption" name="caption" value="<?= old('caption') ?>" maxlength="200" placeholder="Légende affichée au survol">
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label for="gallery-status">Statut</label>
                        <select id="gallery-status" name="content_status">
                            <option value="demo" <?= old('content_status', 'demo') === 'demo' ? 'selected' : '' ?>>Démonstration</option>
                            <option value="verified" <?= old('content_status') === 'verified' ? 'selected' : '' ?>>Vérifiée / officielle</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="gallery-sort">Ordre</label>
                        <input type="number" id="gallery-sort" name="sort_order" value="<?= old('sort_order', '10') ?>" min="0" max="999">
                    </div>
                </div>
                <div class="checkline">
                    <label class="checkbox--admin"><input type="checkbox" name="is_active" value="1" <?= old_checked('is_active', true) ? 'checked' : '' ?>> Visible</label>
                    <label class="checkbox--admin"><input type="checkbox" name="is_featured" value="1" <?= old_checked('is_featured', false) ? 'checked' : '' ?>> Mise en avant</label>
                </div>
                <div class="form-actions">
                    <button class="btn btn--gold btn--small" type="submit">Téléverser <i class="fa-solid fa-upload" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </section>

    <section class="admin-form-card">
        <div class="admin-form-card__head">
            <h2>Catégories</h2>
        </div>
        <div class="admin-form-card__body">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Nom</th><th>Images</th><th>Lien</th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><span class="cell-strong"><?= e($category['name']) ?></span></td>
                                <td><?= (int) $category['image_count'] ?></td>
                                <td><span class="cell-sub"><?= e($category['slug']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <form action="<?= e(route('admin.gallery.categories.store')) ?>" method="post" style="margin-top:1rem;">
                <?= csrf_field() ?>
                <h2 class="admin-form-section">Nouvelle catégorie</h2>
                <div class="field <?= has_error('name') ? 'has-error' : '' ?>">
                    <label for="category-name">Nom <span class="required">*</span></label>
                    <input type="text" id="category-name" name="name" value="<?= old('name') ?>" required maxlength="120" placeholder="Architecture, chambres, table…">
                    <?php if (has_error('name')): ?><span class="field__error"><?= error_for('name') ?></span><?php endif; ?>
                </div>
                <div class="field" style="margin-top:0.7rem;">
                    <label for="category-slug">Lien (optionnel)</label>
                    <input type="text" id="category-slug" name="slug" value="<?= old('slug') ?>" maxlength="120" placeholder="généré depuis le nom">
                    <?php if (has_error('slug')): ?><span class="field__error"><?= error_for('slug') ?></span><?php endif; ?>
                </div>
                <div class="form-actions">
                    <button class="btn btn--ghost btn--small" type="submit">Créer la catégorie</button>
                </div>
            </form>
        </div>
    </section>
</div>

<section class="admin-section" style="margin-top:1.2rem;">
    <div class="admin-section__head">
        <div>
            <h2>Images de la galerie</h2>
            <p><?= count($images) ?> image(s) enregistrée(s)</p>
        </div>
        <a href="<?= e(route('gallery.index')) ?>" target="_blank" rel="noopener noreferrer">Voir la galerie publique</a>
    </div>
    <?php if ($images === []): ?>
        <div class="admin-empty">
            <i class="fa-regular fa-images" aria-hidden="true"></i>
            <p>Aucune image enregistrée.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th></th><th>Légende / alt</th><th>Catégorie</th><th>Statut</th><th>Ordre</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image): ?>
                        <tr>
                            <td><img class="table-thumb" src="<?= e(media_url($image['file_path'])) ?>" alt="" loading="lazy"></td>
                            <td>
                                <span class="cell-strong"><?= e($image['caption'] ?: 'Sans légende') ?></span>
                                <span class="cell-sub"><?= e($image['alt_text']) ?></span>
                            </td>
                            <td><?= e($image['category_name'] ?: '—') ?></td>
                            <td>
                                <?php if ((int) $image['is_active'] === 1): ?>
                                    <span class="status status--confirmed">Visible</span>
                                <?php else: ?>
                                    <span class="status status--cancelled">Masquée</span>
                                <?php endif; ?>
                                <?php if ($image['content_status'] === 'demo'): ?>
                                    <span class="status status--new" style="margin-top:0.3rem;">Démo</span>
                                <?php endif; ?>
                                <?php if ((int) $image['is_featured'] === 1): ?><span class="cell-sub">Mise en avant</span><?php endif; ?>
                            </td>
                            <td><?= (int) $image['sort_order'] ?></td>
                            <td>
                                <form action="<?= e(route('admin.gallery.delete', ['id' => (int) $image['id']])) ?>" method="post"
                                      data-confirm="Supprimer cette image de la galerie ?">
                                    <?= csrf_field() ?>
                                    <button class="table-action table-action--danger" type="submit" aria-label="Supprimer l’image">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
