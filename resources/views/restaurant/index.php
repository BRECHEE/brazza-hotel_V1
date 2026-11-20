<?php
$restaurantName = setting('restaurant_name');
partial('components/page-hero', [
    'kicker' => 'Restaurant',
    'title' => 'Une table<br><em>à révéler.</em>',
    'intro' => 'Le restaurant du Brazza Hotel prépare son identité. Carte, horaires et services seront dévoilés après validation par l’établissement.',
    'image' => 'assets/images/restaurant.jpg',
    'imageAlt' => 'Illustration d’un dîner gastronomique — visuel non contractuel',
    'aside' => 'Carte — à confirmer<br>Horaires — à confirmer',
]);
?>
<section class="section restaurant-page">
    <div class="shell">
        <div class="restaurant-editorial">
            <div class="restaurant-editorial__visual" data-reveal>
                <?php partial('components/picture', [
                    'path' => 'assets/images/gallery-food.jpg',
                    'alt' => 'Illustration d’une salle de restaurant chaleureuse — visuel non contractuel',
                    'sizes' => '(max-width: 900px) 100vw, 42vw',
                    'class' => 'restaurant-editorial__image',
                ]); ?>
                <span class="image-caption">Visuel d’illustration</span>
            </div>
            <div class="restaurant-editorial__copy" data-reveal>
                <span class="eyebrow">L’esprit de la table</span>
                <h2 class="display">Recevoir,<br><em>avec justesse.</em></h2>
                <p class="lead"><?= e(setting('restaurant_intro', 'Un aperçu de la table du Brazza Hotel. La carte et les horaires seront publiés après validation.')) ?></p>
                <p>Nous imaginons une table généreuse et lumineuse, attentive aux produits et aux moments partagés. Ce positionnement éditorial reste à valider avec l’hôtel.</p>
                <p>Le nom officiel du restaurant, sa carte, ses horaires et ses éventuels services (petit-déjeuner, room service, privatisation) seront ajoutés ici dès qu’ils seront communiqués.</p>
                <div class="notice">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    <p><strong>Aucune offre culinaire n’est confirmée à ce stade.</strong> Cette page présente uniquement l’intention éditoriale du site.</p>
                </div>
            </div>
        </div>

        <div class="menu-preview" aria-label="Aperçu de la carte">
            <article class="menu-course" data-reveal>
                <span class="menu-course__index">01 · Pour commencer</span>
                <h2>Entrées</h2>
                <p>Une sélection légère pour ouvrir le repas, à construire avec le chef et l’équipe de l’hôtel.</p>
                <span class="menu-course__status">Sélection à confirmer</span>
            </article>
            <article class="menu-course" data-reveal>
                <span class="menu-course__index">02 · Au centre de la table</span>
                <h2>Plats</h2>
                <p>Des assiettes généreuses, guidées par les produits disponibles et les saisons.</p>
                <span class="menu-course__status">Carte à confirmer</span>
            </article>
            <article class="menu-course" data-reveal>
                <span class="menu-course__index">03 · Pour prolonger</span>
                <h2>Desserts</h2>
                <p>Une note douce pour terminer le repas, à révéler dans la future carte officielle.</p>
                <span class="menu-course__status">Sélection à confirmer</span>
            </article>
        </div>

        <div class="intro-row" style="margin-top: clamp(3rem, 6vw, 5rem);">
            <div class="intro-row__title">
                <span class="eyebrow">Informations pratiques</span>
                <h2 class="display">Horaires<br><em>&amp; services.</em></h2>
            </div>
            <div class="intro-row__copy">
                <p>Les horaires officiels du restaurant n’ont pas encore été communiqués. Ils seront affichés ici dès validation.</p>
                <div class="hours-grid">
                    <div><span>Petit-déjeuner</span><span>À confirmer</span></div>
                    <div><span>Déjeuner</span><span>À confirmer</span></div>
                    <div><span>Dîner</span><span>À confirmer</span></div>
                    <div><span>Room service</span><span>À confirmer</span></div>
                </div>
                <div class="notice" style="margin-top: 1.5rem;">
                    <i class="fa-solid fa-utensils" aria-hidden="true"></i>
                    <p><strong>Réservation de table.</strong> Aucun service de réservation de restaurant n’est encore actif. Contactez l’hôtel pour toute demande.</p>
                </div>
                <div class="form-foot">
                    <a class="btn btn--gold" href="<?= e(route('contact.index')) ?>">Contacter l’hôtel <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php partial('components/cta-band', [
    'ctaTitle' => 'Un séjour<br>à <em>Brazzaville.</em>',
    'ctaText' => 'Partagez vos dates et vos envies. Nous préparerons votre demande avec attention.',
]); ?>
