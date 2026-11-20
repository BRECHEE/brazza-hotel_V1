<?php
/**
 * Compact editorial hero used by interior pages.
 *
 * @var string $kicker
 * @var string $title
 * @var string|null $intro
 * @var string $image
 * @var string $imageAlt
 * @var string|null $aside
 */
$image = $image ?? 'assets/images/lobby.jpg';
$imageAlt = $imageAlt ?? 'Illustration d’un espace hôtelier — visuel non contractuel';
$intro = $intro ?? null;
$aside = $aside ?? null;
?>
<section class="page-hero">
    <div class="page-hero__media">
        <?php partial('components/picture', [
            'path' => $image,
            'alt' => $imageAlt,
            'sizes' => '100vw',
            'loading' => 'eager',
            'fetchpriority' => 'high',
            'class' => 'page-hero__image',
        ]); ?>
    </div>
    <div class="page-hero__shade"></div>
    <div class="shell page-hero__inner">
        <div class="page-hero__copy">
            <span class="eyebrow"><?= e($kicker) ?></span>
            <h1><?= $title ?></h1>
            <?php if ($intro): ?>
                <p class="page-hero__intro"><?= e($intro) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($aside): ?>
            <p class="page-hero__aside">
                <?php foreach (preg_split('/<br\s*\/?>/i', (string) $aside) as $line): ?>
                    <span><?= e(trim($line)) ?></span>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>
    </div>
    <span class="page-hero__credit">Visuel d’illustration</span>
</section>
