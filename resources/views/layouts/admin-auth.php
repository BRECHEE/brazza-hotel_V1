<?php
$pageTitle = (string) ($meta['title'] ?? 'Connexion — Administration');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b0b0b">
    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('assets/css/fontawesome.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin-auth-body">
<main class="admin-auth">
    <aside class="admin-auth__visual">
        <picture>
            <source type="image/webp" srcset="<?= e(url('assets/images/optimized/room-suite-960.webp')) ?> 960w" sizes="50vw">
            <img src="<?= e(asset('assets/images/room-suite.jpg')) ?>" alt="" width="1400" height="933">
        </picture>
        <div class="admin-auth__overlay"></div>
        <a class="admin-brand admin-brand--auth" href="<?= e(route('home')) ?>">
            <img src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="44" height="44">
            <span><strong>Brazza</strong><small>Hôtel · Brazzaville</small></span>
        </a>
        <div class="admin-auth__quote">
            <span class="eyebrow">Espace réservé</span>
            <p>L’art de recevoir commence<br>par une présence <em>discrète.</em></p>
            <small>Gestion des demandes, des chambres et des contenus.</small>
        </div>
        <span class="admin-auth__credit">Visuel d’illustration</span>
    </aside>

    <section class="admin-auth__panel">
        <div class="login-card">
            <?= $content ?>
        </div>
        <p class="admin-auth__legal">© <?= e(current_year()) ?> Brazza Hotel. Toute tentative d’accès non autorisée est enregistrée.</p>
    </section>
</main>
<script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</body>
</html>
