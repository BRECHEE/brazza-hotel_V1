<?php
partial('components/page-hero', [
    'kicker' => 'Chambres & suites',
    'title' => e($room['name']),
    'intro' => $room['tagline'] ?: null,
    'image' => $heroImage,
    'imageAlt' => $images[0]['alt_text'] ?? ('Illustration de ' . $room['name']),
    'aside' => 'Fiche de démonstration<br>Informations à confirmer',
]);
?>
<section class="section room-detail">
    <div class="shell">
        <nav class="breadcrumb" aria-label="Fil d’Ariane">
            <a href="<?= e(route('home')) ?>">Accueil</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <a href="<?= e(route('rooms.index')) ?>">Chambres</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span aria-current="page"><?= e($room['name']) ?></span>
        </nav>

        <div class="room-detail__top">
            <div class="room-detail__gallery">
                <div class="room-detail__main-image">
                    <?php partial('components/picture', [
                        'path' => $heroImage,
                        'alt' => $images[0]['alt_text'] ?? ('Illustration de ' . $room['name']),
                        'sizes' => '(max-width: 900px) 100vw, 58vw',
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                    ]); ?>
                    <?php partial('components/demo-chip', ['label' => 'Visuel d’illustration']); ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="room-detail__thumbs" aria-label="Autres visuels de la chambre">
                        <?php foreach ($images as $image): ?>
                            <a class="room-detail__thumb" href="<?= e(media_url($image['file_path'])) ?>"
                               data-lightbox data-full="<?= e(media_url($image['file_path'])) ?>"
                               data-alt="<?= e($image['alt_text']) ?>"
                               data-caption="<?= e($room['name'] . ' — visuel d’illustration') ?>">
                                <?php partial('components/picture', [
                                    'path' => $image['file_path'],
                                    'alt' => $image['alt_text'],
                                    'sizes' => '120px',
                                ]); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="room-detail__aside">
                <?php partial('components/demo-chip', ['label' => 'Contenu de démonstration']); ?>
                <h2><?= e($room['name']) ?></h2>
                <?php if ($room['tagline']): ?>
                    <p class="room-detail__tagline"><?= e($room['tagline']) ?></p>
                <?php endif; ?>
                <div class="room-detail__price">
                    <?= e(price_display($room['price_per_night'] !== null ? (string) $room['price_per_night'] : null, (string) $room['currency'])) ?>
                    <small>Tarif officiel et conditions à confirmer</small>
                </div>
                <div class="room-detail__facts">
                    <div>
                        <span><i class="fa-regular fa-user" aria-hidden="true"></i> Capacité indicative</span>
                        <span><?= (int) $room['max_occupancy'] ?> personnes</span>
                    </div>
                    <?php if ($room['bed_type']): ?>
                        <div>
                            <span><i class="fa-solid fa-bed" aria-hidden="true"></i> Couchage</span>
                            <span><?= e($room['bed_type']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($room['size_sqm']): ?>
                        <div>
                            <span><i class="fa-solid fa-up-right-and-down-left-from-center" aria-hidden="true"></i> Superficie</span>
                            <span><?= (int) $room['size_sqm'] ?> m²</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="room-detail__cta">
                    <a class="btn btn--gold btn--block" href="<?= e(route('reservation.create', ['room' => $room['id']])) ?>">
                        Demander cette chambre <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <?php if (whatsapp_href() !== null): ?>
                        <a class="btn btn--ghost btn--block" href="<?= e((string) whatsapp_href('Bonjour, je m’intéresse à la ' . $room['name'] . ' au Brazza Hotel.')) ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
                        </a>
                    <?php else: ?>
                        <a class="btn btn--ghost btn--block" href="<?= e(route('contact.index')) ?>">Poser une question</a>
                    <?php endif; ?>
                </div>
                <p class="room-detail__note">
                    Cette demande ne confirme ni la disponibilité ni le tarif. Un membre de l’hôtel vous répondra personnellement.
                </p>
            </aside>
        </div>

        <div class="room-detail__content">
            <div>
                <span class="eyebrow">La chambre</span>
                <h2>Un aperçu<br><em>à valider.</em></h2>
                <p><?= nl2br(e($room['description'])) ?></p>
                <?php if ($room['short_description']): ?>
                    <div class="notice" style="margin-top: 1.5rem;">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <p><?= e($room['short_description']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <span class="eyebrow">Équipements</span>
                <h2>Les détails<br><em>du séjour.</em></h2>
                <p>La liste ci-dessous est indicative et sera confirmée avec l’hôtel pour chaque catégorie.</p>
                <?php if ($amenities !== []): ?>
                    <ul class="amenities">
                        <?php foreach ($amenities as $amenity): ?>
                            <li><i class="fa-solid <?= e($amenity['icon']) ?>" aria-hidden="true"></i><?= e($amenity['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">La liste des équipements sera publiée prochainement.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php partial('components/cta-band', [
    'ctaTitle' => 'Une question<br>sur cette <em>chambre&nbsp;?</em>',
    'ctaText' => 'Précisez vos dates et vos besoins. Nous transmettrons votre demande à l’équipe de l’hôtel.',
]); ?>

<?php partial('components/lightbox'); ?>
