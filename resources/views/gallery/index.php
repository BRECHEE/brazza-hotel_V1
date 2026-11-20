<?php
partial('components/page-hero', [
    'kicker' => 'Galerie',
    'title' => 'Fragments<br><em>de Brazza.</em>',
    'intro' => 'Une collection de visuels d’illustration, en attendant les photographies officielles de l’hôtel et de ses espaces.',
    'image' => 'assets/images/lobby.jpg',
    'imageAlt' => 'Illustration d’une architecture hôtelière au crépuscule — visuel non contractuel',
    'aside' => 'Visuels non contractuels<br>Photographies officielles à venir',
]);
?>
<section class="section gallery-page">
    <div class="shell">
        <div class="intro-row" style="margin-bottom: clamp(2.5rem, 5vw, 4rem);">
            <div class="intro-row__title">
                <span class="eyebrow">Regards</span>
                <h2 class="display">L’atmosphère,<br><em>en images.</em></h2>
            </div>
            <div class="intro-row__copy">
                <p class="lead">Des lumières, des matières et des instants — une première impression à compléter.</p>
                <p>Les photographies présentées ici proviennent de banques d’images et ne représentent pas l’établissement. Elles seront remplacées par des prises de vue officielles.</p>
            </div>
        </div>

        <nav class="gallery-filters" aria-label="Filtrer la galerie par catégorie">
            <a class="gallery-filter <?= $activeCategory === null ? 'is-active' : '' ?>" href="<?= e(route('gallery.index')) ?>">
                Tout <span><?= (int) ($totalImages ?? count($images)) ?></span>
            </a>
            <?php foreach ($categories as $category): ?>
                <a class="gallery-filter <?= $activeCategory === $category['slug'] ? 'is-active' : '' ?>"
                   href="<?= e(route('gallery.index', ['categorie' => $category['slug']])) ?>">
                    <?= e($category['name']) ?> <span><?= (int) $category['image_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($images === []): ?>
            <div class="notice">
                <i class="fa-regular fa-images" aria-hidden="true"></i>
                <p>Aucune image n’est disponible dans cette catégorie pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid gallery-grid--page">
                <?php foreach ($images as $image): ?>
                    <a class="gallery-grid__item" href="<?= e(media_url($image['file_path'])) ?>"
                       data-lightbox
                       data-full="<?= e(media_url($image['file_path'])) ?>"
                       data-alt="<?= e($image['alt_text']) ?>"
                       data-caption="<?= e(($image['caption'] ?: 'Brazza Hotel') . ' — visuel d’illustration') ?>"
                       aria-label="Agrandir : <?= e($image['caption'] ?: $image['alt_text']) ?>">
                        <?php partial('components/picture', [
                            'path' => $image['file_path'],
                            'alt' => $image['alt_text'],
                            'sizes' => '(max-width: 700px) 100vw, 33vw',
                            'class' => 'gallery-grid__image',
                        ]); ?>
                        <span class="gallery-grid__shade"></span>
                        <span class="gallery-grid__caption">
                            <span><?= e($image['caption'] ?: 'Brazza Hotel') ?></span>
                            <i class="fa-solid fa-expand" aria-hidden="true"></i>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-camera" aria-hidden="true"></i>
            <p><strong>Visuels de démonstration.</strong> Photographies issues de banques d’images, non contractuelles. Les photographies officielles de Brazza Hotel seront intégrées après réception et validation.</p>
        </div>
    </div>
</section>

<?php partial('components/lightbox'); ?>
<?php partial('components/cta-band'); ?>
