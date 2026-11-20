<?php
/** @var string $phone */
$navItems = [
    ['label' => 'Accueil', 'route' => 'home', 'path' => '/', 'exact' => true],
    ['label' => 'Chambres', 'route' => 'rooms.index', 'path' => '/chambres', 'exact' => false],
    ['label' => 'Restaurant', 'route' => 'restaurant.index', 'path' => '/restaurant', 'exact' => false],
    ['label' => 'Galerie', 'route' => 'gallery.index', 'path' => '/galerie', 'exact' => false],
    ['label' => 'À propos', 'route' => 'about.index', 'path' => '/a-propos', 'exact' => false],
    ['label' => 'Contact', 'route' => 'contact.index', 'path' => '/contact', 'exact' => false],
];
?>
<header class="site-header" data-header>
    <div class="shell header-inner">
        <a class="brand" href="<?= e(route('home')) ?>" aria-label="Brazza Hotel — Accueil">
            <img class="brand__mark" src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="48" height="48">
            <span class="brand__type">
                <span class="brand__name">Brazza</span>
                <span class="brand__sub">Hôtel · Brazzaville</span>
            </span>
        </a>

        <nav class="desktop-nav" aria-label="Navigation principale">
            <?php foreach ($navItems as $item): ?>
                <a class="nav-link <?= e(nav_class($item['path'], $item['exact'])) ?>"
                   href="<?= e(route($item['route'])) ?>"
                   <?= is_current($item['path'], $item['exact']) ? 'aria-current="page"' : '' ?>>
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="header-actions">
            <?php if (filled($phone)): ?>
                <a class="header-phone" href="<?= e(phone_href($phone)) ?>" aria-label="Appeler le Brazza Hotel">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    <span><?= e($phone) ?></span>
                </a>
            <?php endif; ?>
            <button class="theme-toggle" type="button" data-theme-toggle
                    aria-label="Activer le mode clair" aria-pressed="false" title="Activer le mode clair">
                <span class="theme-toggle__sun"><i class="fa-regular fa-sun" aria-hidden="true"></i></span>
                <span class="theme-toggle__moon"><i class="fa-regular fa-moon" aria-hidden="true"></i></span>
            </button>
            <a class="btn btn--gold btn--small header-cta" href="<?= e(route('reservation.create')) ?>">
                <span>Réserver</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <button class="menu-toggle" type="button" aria-controls="mobile-menu" aria-expanded="false"
                    aria-label="Ouvrir le menu" data-menu-toggle>
                <span></span><span></span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true" inert data-mobile-menu>
    <div class="mobile-menu__top">
        <span class="eyebrow">Brazza Hotel</span>
        <button class="mobile-close" type="button" aria-label="Fermer le menu" data-menu-close>
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <nav class="mobile-nav" aria-label="Navigation mobile">
        <?php foreach ($navItems as $index => $item): ?>
            <a href="<?= e(route($item['route'])) ?>"
               class="<?= e(nav_class($item['path'], $item['exact'])) ?>"
               <?= is_current($item['path'], $item['exact']) ? 'aria-current="page"' : '' ?>>
                <span class="mobile-nav__index">0<?= $index + 1 ?></span>
                <?= e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="mobile-menu__bottom">
        <a class="btn btn--gold" href="<?= e(route('reservation.create')) ?>">Demander une chambre</a>
        <div class="mobile-menu__contact">
            <span>Brazzaville, République du Congo</span>
            <?php if (filled($phone)): ?>
                <a href="<?= e(phone_href($phone)) ?>"><?= e($phone) ?></a>
            <?php else: ?>
                <span class="text-muted">Téléphone à confirmer</span>
            <?php endif; ?>
        </div>
    </div>
</div>
