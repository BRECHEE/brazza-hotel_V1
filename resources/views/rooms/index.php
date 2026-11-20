<?php
partial('components/page-hero', [
    'kicker' => 'Chambres & suites',
    'title' => 'Des espaces<br><em>à votre image.</em>',
    'intro' => 'Trois catégories sont présentées comme base éditoriale. Chaque fiche, chaque équipement et chaque tarif sera validé avec l’hôtel avant publication.',
    'image' => 'assets/images/room-suite.jpg',
    'imageAlt' => 'Illustration d’une chambre lumineuse — visuel non contractuel',
    'aside' => 'Catégories — aperçu<br>Tarifs — sur demande',
]);
?>
<section class="section rooms-page">
    <div class="shell">
        <div class="intro-row">
            <div class="intro-row__title">
                <span class="eyebrow">Séjourner</span>
                <h2 class="display">Le calme,<br><em>en héritage.</em></h2>
            </div>
            <div class="intro-row__copy">
                <p class="lead">Des espaces pensés pour ralentir, se retrouver et laisser la ville dehors.</p>
                <p>Les chambres du Brazza Hotel sont ici présentées à titre d’aperçu. Les descriptifs complets, les équipements officiels et les tarifs seront renseignés après validation par l’établissement.</p>
                <div class="notice">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    <p><strong>Information importante.</strong> Aucune disponibilité n’est affichée en temps réel. Pour toute date, adressez-nous une demande : l’équipe vérifiera les possibilités et reviendra vers vous.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--tight rooms-page" aria-label="Catégories de chambres">
    <div class="shell">
        <div class="room-list">
            <?php foreach ($rooms as $index => $room): ?>
                <?php $roomUrl = route('rooms.show', ['slug' => $room['slug']]); ?>
                <article class="room-row" data-reveal>
                    <div class="room-row__visual">
                        <?php partial('components/picture', [
                            'path' => $room['image_path'] ?: 'assets/images/placeholder.svg',
                            'alt' => $room['image_alt'] ?: ('Illustration de ' . $room['name']),
                            'sizes' => '(max-width: 900px) 100vw, 55vw',
                            'class' => 'room-row__image',
                        ]); ?>
                        <?php partial('components/demo-chip', ['label' => 'Aperçu']); ?>
                        <span class="room-row__index">0<?= $index + 1 ?></span>
                    </div>
                    <div class="room-row__body">
                        <span class="eyebrow">Catégorie 0<?= $index + 1 ?></span>
                        <h2><a href="<?= e($roomUrl) ?>"><?= e($room['name']) ?></a></h2>
                        <?php if ($room['tagline']): ?>
                            <p class="room-row__tagline"><?= e($room['tagline']) ?></p>
                        <?php endif; ?>
                        <p><?= e($room['short_description']) ?></p>
                        <div class="room-facts">
                            <span><i class="fa-regular fa-user" aria-hidden="true"></i><?= (int) $room['max_occupancy'] ?> personnes</span>
                            <?php if ($room['bed_type']): ?>
                                <span><i class="fa-solid fa-bed" aria-hidden="true"></i><?= e($room['bed_type']) ?></span>
                            <?php endif; ?>
                            <?php if ($room['size_sqm']): ?>
                                <span><i class="fa-solid fa-up-right-and-down-left-from-center" aria-hidden="true"></i><?= (int) $room['size_sqm'] ?> m²</span>
                            <?php endif; ?>
                        </div>
                        <div class="room-row__foot">
                            <div class="room-row__price">
                                <?= e(price_display($room['price_per_night'] !== null ? (string) $room['price_per_night'] : null, (string) $room['currency'])) ?>
                                <small>Tarif officiel à confirmer</small>
                            </div>
                            <div class="room-row__actions">
                                <a class="btn btn--ghost btn--small" href="<?= e($roomUrl) ?>">Découvrir</a>
                                <a class="btn btn--gold btn--small" href="<?= e(route('reservation.create', ['room' => $room['id']])) ?>">Demander</a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="rooms-disclaimer">
            <p><strong>Capacités et équipements</strong>Les informations affichées sont indicatives. Elles ne remplacent pas les conditions officielles de l’hôtel.</p>
            <p><strong>Tarifs</strong>Aucun prix n’est publié tant qu’il n’a pas été communiqué officiellement. Les demandes sont étudiées au cas par cas.</p>
        </div>
    </div>
</section>

<?php partial('components/cta-band'); ?>
