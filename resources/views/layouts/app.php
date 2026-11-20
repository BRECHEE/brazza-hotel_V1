<?php
$pageTitle = (string) ($meta['title'] ?? 'Brazza Hotel');
$pageDescription = (string) ($meta['description'] ?? 'Brazza Hotel, Brazzaville, République du Congo.');
$pageImage = (string) ($meta['image'] ?? asset('assets/images/og-brazza-hotel.jpg'));
$canonical = (string) ($meta['canonical'] ?? url());
$robots = (string) ($meta['robots'] ?? 'index, follow');
$useGsap = $useGsap ?? is_current('/', true);
$phone = setting('hotel_phone', (string) config('app.hotel.phone'));
$whatsapp = setting('hotel_whatsapp', (string) config('app.hotel.whatsapp'));
$instagram = setting('instagram_url');
$facebook = setting('facebook_url');

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Hotel',
            '@id' => url('/') . '#hotel',
            'name' => setting('hotel_name', 'Brazza Hotel'),
            'description' => 'Brazza Hotel, adresse hôtelière à Brazzaville, République du Congo.',
            'url' => url('/'),
            'image' => $pageImage,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => setting('hotel_city', 'Brazzaville'),
                'addressCountry' => 'CG',
            ],
            'areaServed' => 'Brazzaville',
        ],
        [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => 'Brazza Hotel',
            'inLanguage' => 'fr-CG',
        ],
    ],
];
if ($phone !== '') {
    $schema['@graph'][0]['telephone'] = $phone;
}
if ($instagram !== '' || $facebook !== '') {
    $schema['@graph'][0]['sameAs'] = array_values(array_filter([$instagram, $facebook]));
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="robots" content="<?= e($robots) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#101010" data-theme-color>
    <meta name="geo.region" content="CG-BZV">
    <meta name="geo.placename" content="Brazzaville">

    <script nonce="<?= e(csp_nonce()) ?>">
        (() => {
            document.documentElement.classList.add("is-loading");
            try {
                const saved = localStorage.getItem("brazza-theme");
                if (saved === "light" || saved === "dark") {
                    document.documentElement.dataset.theme = saved;
                }
            } catch (error) {
                /* Storage can be unavailable in private browsing. */
            }
            // Never let a failed enhancement keep the interface covered.
            window.setTimeout(() => {
                document.documentElement.classList.remove("is-loading");
                const loader = document.querySelector("[data-site-loader]");
                if (loader) {
                    loader.classList.add("is-hidden");
                    window.setTimeout(() => loader.remove(), 700);
                }
            }, 6000);
        })();
    </script>
    <noscript>
        <style>.site-loader { display: none !important; } html.is-loading body { overflow: auto !important; }</style>
    </noscript>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Brazza Hotel">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($pageImage) ?>">
    <meta property="og:image:alt" content="Brazza Hotel — Brazzaville, République du Congo">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($pageDescription) ?>">
    <meta name="twitter:image" content="<?= e($pageImage) ?>">

    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preload" href="<?= e(asset('assets/fonts/cormorant-garamond-300.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(asset('assets/fonts/manrope-400.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('assets/css/fontawesome.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php if ($useGsap): ?>
        <link rel="preload" href="<?= e(asset('assets/js/gsap.min.js')) ?>" as="script">
    <?php endif; ?>

    <script type="application/ld+json" nonce="<?= e(csp_nonce()) ?>"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body class="<?= e(body_class($bodyClass ?? '')) ?>">
<div class="site-loader" data-site-loader role="status" aria-live="polite" aria-label="Chargement de Brazza Hotel">
    <div class="site-loader__brand">
        <img src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="54" height="54">
        <span class="site-loader__name">Brazza <em>Hotel</em></span>
        <span class="site-loader__place">Brazzaville · République du Congo</span>
    </div>
    <div class="loader" aria-hidden="true">
        <span class="loader-text">chargement</span>
        <span class="load"></span>
    </div>
</div>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<?php partial('components/header', ['phone' => $phone]); ?>

<main id="main">
    <?= $content ?>
</main>

<?php partial('components/footer', ['phone' => $phone, 'whatsapp' => $whatsapp]); ?>

<?php if (whatsapp_href('Bonjour Brazza Hotel, je souhaite obtenir des informations sur vos chambres.') !== null): ?>
    <a class="floating-whatsapp"
       href="<?= e((string) whatsapp_href('Bonjour Brazza Hotel, je souhaite obtenir des informations sur vos chambres.')) ?>"
       target="_blank" rel="noopener noreferrer"
       aria-label="Contacter Brazza Hotel sur WhatsApp">
        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
    </a>
<?php endif; ?>

<?php if ($useGsap): ?>
    <script src="<?= e(asset('assets/js/gsap.min.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/ScrollTrigger.min.js')) ?>" defer></script>
<?php endif; ?>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
<noscript><div class="noscript-note">Ce site fonctionne sans JavaScript. Les animations sont simplement désactivées.</div></noscript>
</body>
</html>
