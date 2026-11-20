<?php
/** @var array<string, mixed> $adminUser */
$unreadMessages = \App\Models\ContactMessage::unreadCount();
$reservationStats = \App\Models\Reservation::stats();
$adminName = (string) ($adminUser['name'] ?? 'Administrateur');
$roleLabels = ['owner' => 'Direction', 'manager' => 'Gestion', 'editor' => 'Édition'];

$navItems = [
    ['route' => 'admin.dashboard', 'path' => '/admin', 'exact' => true, 'label' => 'Tableau de bord', 'icon' => 'fa-gauge-high', 'badge' => 0],
    ['route' => 'admin.reservations.index', 'path' => '/admin/reservations', 'exact' => false, 'label' => 'Réservations', 'icon' => 'fa-calendar-check', 'badge' => $reservationStats['new']],
    ['route' => 'admin.rooms.index', 'path' => '/admin/chambres', 'exact' => false, 'label' => 'Chambres', 'icon' => 'fa-bed', 'badge' => 0],
    ['route' => 'admin.gallery.index', 'path' => '/admin/galerie', 'exact' => false, 'label' => 'Galerie', 'icon' => 'fa-images', 'badge' => 0],
    ['route' => 'admin.messages.index', 'path' => '/admin/messages', 'exact' => false, 'label' => 'Messages', 'icon' => 'fa-envelope', 'badge' => $unreadMessages],
    ['route' => 'admin.settings.index', 'path' => '/admin/parametres', 'exact' => false, 'label' => 'Informations', 'icon' => 'fa-sliders', 'badge' => 0],
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — Administration Brazza Hotel</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b0b0b">
    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('assets/css/fontawesome.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
<a class="skip-link" href="#admin-content">Aller au contenu</a>

<div class="admin-shell" data-admin-shell>
    <aside class="admin-sidebar" id="admin-sidebar">
        <a class="admin-brand" href="<?= e(route('admin.dashboard')) ?>" aria-label="Brazza Hotel — Tableau de bord">
            <img src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="42" height="42">
            <span>
                <strong>Brazza</strong>
                <small>Espace hôtel</small>
            </span>
        </a>

        <nav class="admin-nav" aria-label="Navigation administration">
            <span class="admin-nav__label">Pilotage</span>
            <?php foreach ($navItems as $item): ?>
                <a href="<?= e(route($item['route'])) ?>"
                   class="<?= admin_is($item['path'], $item['exact']) ? 'is-active' : '' ?>"
                   <?= admin_is($item['path'], $item['exact']) ? 'aria-current="page"' : '' ?>>
                    <i class="fa-solid <?= e($item['icon']) ?>" aria-hidden="true"></i>
                    <span><?= e($item['label']) ?></span>
                    <?php if ((int) $item['badge'] > 0): ?>
                        <em><?= (int) $item['badge'] ?></em>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

            <span class="admin-nav__label admin-nav__label--second">Compte</span>
            <a href="<?= e(route('admin.profile.index')) ?>" class="<?= admin_is('/admin/profil', false) ? 'is-active' : '' ?>">
                <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                <span>Mon compte</span>
            </a>
            <a href="<?= e(route('home')) ?>" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                <span>Voir le site</span>
            </a>
        </nav>

        <div class="admin-sidebar__bottom">
            <div class="admin-user">
                <span class="admin-avatar"><?= e(initials($adminName)) ?></span>
                <div>
                    <strong><?= e($adminName) ?></strong>
                    <small><?= e($roleLabels[(string) ($adminUser['role'] ?? 'manager')] ?? 'Gestion') ?></small>
                </div>
            </div>
            <form action="<?= e(route('admin.logout')) ?>" method="post">
                <?= csrf_field() ?>
                <button class="admin-logout" type="submit">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-menu-toggle" type="button" aria-controls="admin-sidebar" aria-expanded="false"
                    aria-label="Ouvrir le menu d’administration" data-admin-toggle>
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-topbar__title">
                <span>Administration</span>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <div class="admin-topbar__actions">
                <?php if ($reservationStats['new'] > 0): ?>
                    <a class="admin-topbar__alert" href="<?= e(route('admin.reservations.index', ['statut' => 'new'])) ?>">
                        <span><?= (int) $reservationStats['new'] ?></span> nouvelle<?= $reservationStats['new'] > 1 ? 's' : '' ?> demande<?= $reservationStats['new'] > 1 ? 's' : '' ?>
                    </a>
                <?php endif; ?>
                <a class="admin-topbar__site" href="<?= e(route('home')) ?>" target="_blank" rel="noopener noreferrer" aria-label="Voir le site public">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                </a>
                <form action="<?= e(route('admin.logout')) ?>" method="post">
                    <?= csrf_field() ?>
                    <button class="admin-topbar__logout" type="submit" aria-label="Se déconnecter">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </header>

        <main class="admin-content" id="admin-content">
            <?php partial('components/alerts'); ?>
            <?= $content ?>
        </main>

        <footer class="admin-footer">
            <span>Brazza Hotel · Administration</span>
            <span>Session sécurisée · <?= e(current_year()) ?></span>
        </footer>
    </div>
</div>

<div class="admin-overlay" data-admin-overlay></div>
<script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</body>
</html>
