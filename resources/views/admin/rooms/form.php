<?php
$isEdit = $room !== null;
$nameDefault = (string) ($room['name'] ?? '');
$slugDefault = (string) ($room['slug'] ?? '');
$shortDefault = (string) ($room['short_description'] ?? '');
$descriptionDefault = (string) ($room['description'] ?? '');
$taglineDefault = (string) ($room['tagline'] ?? '');
$bedDefault = (string) ($room['bed_type'] ?? '');
$priceDefault = $room && $room['price_per_night'] !== null ? (string) $room['price_per_night'] : '';
$sizeDefault = $room && $room['size_sqm'] !== null ? (string) $room['size_sqm'] : '';
$occupancyDefault = (string) ($room['max_occupancy'] ?? '2');
$sortDefault = (string) ($room['sort_order'] ?? '10');
$metaTitleDefault = (string) ($room['meta_title'] ?? '');
$metaDescriptionDefault = (string) ($room['meta_description'] ?? '');
$statusDefault = (string) ($room['content_status'] ?? 'demo');
?>
<a class="admin-back" href="<?= e(route('admin.rooms.index')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Toutes les catégories</a>

<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2><?= $isEdit ? e($room['name']) : 'Nouvelle catégorie' ?></h2>
        <p><?= $isEdit ? 'Modifiez la fiche, les tarifs et les photographies.' : 'Créez une catégorie puis ajoutez ses photographies.' ?></p>
    </div>
    <?php if ($isEdit && (int) $room['is_active'] === 1): ?>
        <div class="admin-page-head__actions">
            <a class="btn btn--ghost btn--small" href="<?= e(route('rooms.show', ['slug' => $room['slug']])) ?>" target="_blank" rel="noopener noreferrer">
                Voir sur le site <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<form action="<?= e($formAction) ?>" method="post" class="admin-form-card">
    <?= csrf_field() ?>
    <div class="admin-form-card__body">
        <h2 class="admin-form-section">Informations éditoriales</h2>
        <div class="form-grid">
            <div class="field <?= has_error('name') ? 'has-error' : '' ?>">
                <label for="room-name">Nom de la catégorie <span class="required">*</span></label>
                <input type="text" id="room-name" name="name" value="<?= old('name', $nameDefault) ?>" required minlength="2" maxlength="120">
                <?php if (has_error('name')): ?><span class="field__error"><?= error_for('name') ?></span><?php endif; ?>
            </div>
            <div class="field <?= has_error('slug') ? 'has-error' : '' ?>">
                <label for="room-slug">Lien (slug)</label>
                <input type="text" id="room-slug" name="slug" value="<?= old('slug', $slugDefault) ?>" maxlength="160" placeholder="généré depuis le nom si vide">
                <span class="admin-help">Lettres minuscules, chiffres et tirets uniquement.</span>
                <?php if (has_error('slug')): ?><span class="field__error"><?= error_for('slug') ?></span><?php endif; ?>
            </div>
            <div class="field field--full <?= has_error('tagline') ? 'has-error' : '' ?>">
                <label for="room-tagline">Accroche</label>
                <input type="text" id="room-tagline" name="tagline" value="<?= old('tagline', $taglineDefault) ?>" maxlength="180" placeholder="Une phrase courte et évocatrice">
                <?php if (has_error('tagline')): ?><span class="field__error"><?= error_for('tagline') ?></span><?php endif; ?>
            </div>
            <div class="field field--full <?= has_error('short_description') ? 'has-error' : '' ?>">
                <label for="room-short">Description courte <span class="required">*</span></label>
                <textarea id="room-short" name="short_description" rows="3" required minlength="10" maxlength="400"><?= old('short_description', $shortDefault) ?></textarea>
                <?php if (has_error('short_description')): ?><span class="field__error"><?= error_for('short_description') ?></span><?php endif; ?>
            </div>
            <div class="field field--full <?= has_error('description') ? 'has-error' : '' ?>">
                <label for="room-description">Description complète <span class="required">*</span></label>
                <textarea id="room-description" name="description" rows="8" required minlength="20"><?= old('description', $descriptionDefault) ?></textarea>
                <span class="admin-help">Décrivez la chambre avec précision. Ne publiez pas d’équipement non confirmé comme « vérifié ».</span>
                <?php if (has_error('description')): ?><span class="field__error"><?= error_for('description') ?></span><?php endif; ?>
            </div>
        </div>

        <h2 class="admin-form-section">Capacité &amp; tarif</h2>
        <div class="form-grid">
            <div class="field <?= has_error('max_occupancy') ? 'has-error' : '' ?>">
                <label for="room-occupancy">Capacité (personnes) <span class="required">*</span></label>
                <input type="number" id="room-occupancy" name="max_occupancy" value="<?= old('max_occupancy', $occupancyDefault) ?>" min="1" max="20" required>
                <?php if (has_error('max_occupancy')): ?><span class="field__error"><?= error_for('max_occupancy') ?></span><?php endif; ?>
            </div>
            <div class="field <?= has_error('bed_type') ? 'has-error' : '' ?>">
                <label for="room-bed">Couchage</label>
                <input type="text" id="room-bed" name="bed_type" value="<?= old('bed_type', $bedDefault) ?>" maxlength="80" placeholder="Lit king size">
            </div>
            <div class="field <?= has_error('size_sqm') ? 'has-error' : '' ?>">
                <label for="room-size">Superficie (m²)</label>
                <input type="number" id="room-size" name="size_sqm" value="<?= old('size_sqm', $sizeDefault) ?>" min="1" max="2000">
                <?php if (has_error('size_sqm')): ?><span class="field__error"><?= error_for('size_sqm') ?></span><?php endif; ?>
            </div>
            <div class="field <?= has_error('price') ? 'has-error' : '' ?>">
                <label for="room-price">Tarif par nuit (FCFA)</label>
                <input type="text" id="room-price" name="price_per_night" value="<?= old('price_per_night', $priceDefault) ?>" maxlength="20" placeholder="Laisser vide = sur demande">
                <span class="admin-help">Laisser vide tant que le tarif officiel n’est pas fourni. Devise : XAF.</span>
                <?php if (has_error('price')): ?><span class="field__error"><?= error_for('price') ?></span><?php endif; ?>
            </div>
            <div class="field <?= has_error('sort_order') ? 'has-error' : '' ?>">
                <label for="room-sort">Ordre d’affichage</label>
                <input type="number" id="room-sort" name="sort_order" value="<?= old('sort_order', $sortDefault) ?>" min="0" max="999">
            </div>
        </div>

        <h2 class="admin-form-section">Publication</h2>
        <div class="form-grid">
            <div class="field">
                <label for="room-status">Statut du contenu</label>
                <select id="room-status" name="content_status">
                    <option value="demo" <?= old('content_status', $statusDefault) === 'demo' ? 'selected' : '' ?>>Démonstration — à valider</option>
                    <option value="verified" <?= old('content_status', $statusDefault) === 'verified' ? 'selected' : '' ?>>Vérifié par l’hôtel</option>
                </select>
                <span class="admin-help">Le badge « Aperçu » disparaît lorsque le contenu est marqué vérifié.</span>
            </div>
            <div class="field">
                <span class="field__label">Visibilité</span>
                <div class="checkline">
                    <label class="checkbox--admin">
                        <input type="checkbox" name="is_active" value="1" <?= old_checked('is_active', $room ? (bool) $room['is_active'] : true) ? 'checked' : '' ?>>
                        Publiée sur le site
                    </label>
                    <label class="checkbox--admin">
                        <input type="checkbox" name="is_featured" value="1" <?= old_checked('is_featured', $room ? (bool) $room['is_featured'] : true) ? 'checked' : '' ?>>
                        Mise en avant
                    </label>
                </div>
            </div>
            <div class="field">
                <label for="meta-title">Titre SEO</label>
                <input type="text" id="meta-title" name="meta_title" value="<?= old('meta_title', $metaTitleDefault) ?>" maxlength="160">
            </div>
            <div class="field">
                <label for="meta-description">Description SEO</label>
                <input type="text" id="meta-description" name="meta_description" value="<?= old('meta_description', $metaDescriptionDefault) ?>" maxlength="300">
            </div>
        </div>

        <div class="form-actions form-actions--between">
            <a class="btn btn--ghost btn--small" href="<?= e(route('admin.rooms.index')) ?>">Annuler</a>
            <button class="btn btn--gold btn--small" type="submit">
                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la catégorie' ?>
                <i class="fa-solid fa-check" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
    <section class="admin-section" style="margin-top: 1.2rem;">
        <div class="admin-section__head">
            <div>
                <h2>Photographies</h2>
                <p>JPG, PNG ou WebP · 4 Mo maximum · 10 images par catégorie.</p>
            </div>
        </div>
        <div class="admin-section__body">
            <?php if ($images === []): ?>
                <p class="admin-muted">Aucune photographie. Ajoutez au moins une image pour illustrer la fiche.</p>
            <?php else: ?>
                <div class="image-manager">
                    <?php foreach ($images as $image): ?>
                        <div class="managed-image">
                            <?php if ((int) $image['is_primary'] === 1): ?><span class="primary-tag">Principale</span><?php endif; ?>
                            <img src="<?= e(media_url($image['file_path'])) ?>" alt="<?= e($image['alt_text']) ?>" loading="lazy">
                            <div class="managed-image__meta">
                                <span title="<?= e($image['alt_text']) ?>"><?= e($image['alt_text']) ?></span>
                                <form action="<?= e(route('admin.rooms.images.delete', ['imageId' => (int) $image['id']])) ?>" method="post"
                                      data-confirm="Supprimer cette photographie ?">
                                    <?= csrf_field() ?>
                                    <button class="managed-image__delete" type="submit" aria-label="Supprimer l’image">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="upload-zone" action="<?= e(route('admin.rooms.images.store', ['id' => (int) $room['id']])) ?>"
                  method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <h3>Ajouter des photographies</h3>
                <p>Les fichiers sont vérifiés côté serveur et renommés aléatoirement.</p>
                <div class="form-grid">
                    <div class="field field--full">
                        <label for="room-images">Fichiers image</label>
                        <input type="file" id="room-images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required data-image-input="#room-file-list">
                        <span class="file-list" id="room-file-list">Aucun fichier sélectionné</span>
                    </div>
                    <div class="field field--full">
                        <label for="room-alt">Texte alternatif</label>
                        <input type="text" id="room-alt" name="alt_text" maxlength="180" placeholder="Description de l’image pour l’accessibilité">
                    </div>
                </div>
                <div class="checkline">
                    <label class="checkbox--admin">
                        <input type="checkbox" name="is_primary" value="1">
                        Définir la première image comme principale (si aucune n’existe)
                    </label>
                </div>
                <div class="form-actions">
                    <button class="btn btn--gold btn--small" type="submit">Téléverser <i class="fa-solid fa-upload" aria-hidden="true"></i></button>
                </div>
            </form>

            <div class="admin-note" style="margin-top: 1rem; margin-bottom: 0;">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                <p><strong>Photographies d’illustration.</strong> Les images de démonstration sont référencées dans la base mais ne sont pas supprimées du dossier public. Les futurs fichiers téléversés sont stockés dans <code>public/assets/uploads/rooms/</code>.</p>
            </div>
        </div>
    </section>
<?php endif; ?>
