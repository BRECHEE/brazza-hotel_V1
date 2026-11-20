<?php
partial('components/page-hero', [
    'kicker' => 'À propos',
    'title' => 'Une maison,<br><em>une attention.</em>',
    'intro' => 'Brazza Hotel se présente comme une adresse contemporaine à Brazzaville. Son histoire officielle, sa philosophie et ses engagements restent à confirmer.',
    'image' => 'assets/images/about.jpg',
    'imageAlt' => 'Illustration d’un espace d’accueil hôtelier — visuel non contractuel',
    'aside' => 'Présentation à valider<br>Photographies officielles à venir',
]);
?>
<section class="section about-page">
    <div class="shell">
        <div class="about-story">
            <div class="about-story__copy" data-reveal>
                <span class="eyebrow">La maison</span>
                <h2 class="display">Une adresse<br><em>à habiter.</em></h2>
                <p class="lead"><?= e(setting('about_intro', 'Brazza Hotel est présenté ici comme une adresse contemporaine à Brazzaville.')) ?></p>
                <p>La ville impose son rythme, sa lumière et sa chaleur. L’hôtel se pense comme un point d’équilibre : un lieu où l’on arrive, où l’on respire, et où l’on se sent attendu.</p>
                <p>Ce texte est une proposition éditoriale. L’histoire de l’établissement, ses fondateurs, son architecture et ses engagements officiels seront intégrés après validation par la direction.</p>
                <div class="notice">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    <p><strong>Information éditoriale.</strong> Aucun fait historique, aucune distinction et aucun label ne sont affirmés sur cette page tant qu’ils n’ont pas été confirmés par l’hôtel.</p>
                </div>
            </div>
            <div class="about-story__visual" data-reveal>
                <?php partial('components/picture', [
                    'path' => 'assets/images/lobby.jpg',
                    'alt' => 'Illustration d’une architecture hôtelière au crépuscule — visuel non contractuel',
                    'sizes' => '(max-width: 900px) 100vw, 38vw',
                ]); ?>
                <span class="image-caption">Visuel d’illustration</span>
            </div>
        </div>
    </div>
</section>

<section class="section section--tight about-page" aria-labelledby="values-title">
    <div class="shell">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Notre philosophie</span>
                <h2 id="values-title" class="display">Trois idées<br><em>simples.</em></h2>
            </div>
            <div class="section-heading__aside">
                <p>Les valeurs ci-dessous constituent le cadre éditorial proposé pour le site. Elles devront être validées et ajustées avec l’hôtel.</p>
                <?php partial('components/demo-chip', ['label' => 'Texte de démonstration']); ?>
            </div>
        </div>
        <div class="values-grid">
            <article class="value-card" data-reveal>
                <span class="value-card__number">01</span>
                <h2>L’attention</h2>
                <p>Accueillir sans excès, comprendre avant de proposer, et faire de chaque détail une attention discrète.</p>
            </article>
            <article class="value-card" data-reveal>
                <span class="value-card__number">02</span>
                <h2>Le temps</h2>
                <p>Laisser à chacun la liberté de son rythme, entre un séjour studieux, une escale paisible ou un moment partagé.</p>
            </article>
            <article class="value-card" data-reveal>
                <span class="value-card__number">03</span>
                <h2>L’ancrage</h2>
                <p>Puiser dans Brazzaville une inspiration sensible, loin des clichés, et célébrer la ville avec justesse.</p>
            </article>
        </div>
    </div>
</section>

<section class="about-quote">
    <div class="shell">
        <span class="eyebrow eyebrow--plain">Notre promesse</span>
        <blockquote>« Une hospitalité qui se souvient <em>des prénoms.</em> »</blockquote>
        <cite>Proposition éditoriale — à valider avec l’hôtel</cite>
    </div>
</section>

<section class="section about-page">
    <div class="shell">
        <div class="intro-row">
            <div class="intro-row__title">
                <span class="eyebrow">À Brazzaville</span>
                <h2 class="display">Une ville<br><em>à découvrir.</em></h2>
            </div>
            <div class="intro-row__copy">
                <p class="lead">Brazzaville, sur la rive droite du fleuve Congo, offre un mélange de vitalité, de mémoire et de paysages uniques.</p>
                <p>Les recommandations de visite, les partenariats et les expériences proposées par l’hôtel seront publiés ici après validation. En attendant, notre équipe reste disponible pour vous accompagner dans la préparation de votre séjour.</p>
                <div class="form-foot">
                    <a class="btn btn--gold" href="<?= e(route('contact.index')) ?>">Nous rencontrer <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="text-link" href="<?= e(route('gallery.index')) ?>">Voir la galerie</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php partial('components/cta-band'); ?>
