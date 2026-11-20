<?php
$pageTitle = (string) ($meta['title'] ?? 'Brazza Hotel');
$robots = (string) ($meta['robots'] ?? 'noindex, nofollow');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="robots" content="<?= e($robots) ?>">
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#101010" data-theme-color>
    <script nonce="<?= e(csp_nonce()) ?>">
        (() => {
            try {
                const saved = localStorage.getItem("brazza-theme");
                if (saved === "light" || saved === "dark") {
                    document.documentElement.dataset.theme = saved;
                }
            } catch (error) {}
        })();
    </script>
    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('assets/css/fontawesome.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body class="site-body minimal-body">
    <a class="skip-link" href="#main">Aller au contenu</a>
    <main id="main" class="minimal-main">
        <div class="minimal-panel">
            <a class="brand brand--minimal" href="<?= e(route('home')) ?>">
                <img class="brand__mark" src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="48" height="48">
                <span class="brand__type">
                    <span class="brand__name">Brazza</span>
                    <span class="brand__sub">Hôtel · Brazzaville</span>
                </span>
            </a>
            <?= $content ?>
        </div>
    </main>
</body>
</html>
