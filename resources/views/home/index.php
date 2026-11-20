<?php
$today = new DateTimeImmutable('today');
$tomorrow = $today->modify('+1 day');
$heroImage = 'assets/images/hero.jpg';
$heroAlt = 'Illustration d’un espace extérieur avec piscine au coucher du soleil — visuel non contractuel';

$galleryClasses = ['gallery-grid__item--wide', 'gallery-grid__item--tall', '', '', 'gallery-grid__item--wide', ''];
?>
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__media">
        <?php partial('components/picture', [
            'path' => $heroImage,
            'alt' => $heroAlt,
            'sizes' => '100vw',
            'loading' => 'eager',
            'fetchpriority' => 'high',
            'class' => 'hero__image',
        ]); ?>
    </div>
    <div class="hero__wash"></div>
    <div class="hero__grain" aria-hidden="true"></div>

    <div class="shell hero__inner">
        <div class="hero__content">
            <p class="hero__kicker">
                <span class="hero__kicker-line" aria-hidden="true"></span>
                Brazzaville · République du Congo
            </p>
            <h1 id="hero-title">
                L’art de recevoir,
                <em>au cœur de Brazzaville.</em>
            </h1>
            <p class="hero__lead">
                Une adresse contemporaine où la lumière, le calme et l’attention dessinent
                un séjour à votre rythme.
            </p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="<?= e(route('reservation.create')) ?>">
                    <span>Demander une chambre</span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
                <a class="btn btn--ghost" href="<?= e(route('about.index')) ?>">
                    <span>Découvrir l’hôtel</span>
                </a>
            </div>
        </div>

        <div class="hero__booking-wrap">
            <form class="booking-bar" action="<?= e(route('reservation.create')) ?>" method="get" aria-label="Préparer une demande de réservation">
                <div class="booking-bar__intro">
                    <span class="booking-bar__title">Votre séjour</span>
                    <span class="booking-bar__hint">Demande sans engagement</span>
                </div>
                <label class="booking-field">
                    <span class="booking-field__label"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Arrivée</span>
                    <input type="date" name="check_in" min="<?= e($today->format('Y-m-d')) ?>" value="<?= e($today->format('Y-m-d')) ?>" required>
                </label>
                <label class="booking-field">
                    <span class="booking-field__label"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i> Départ</span>
                    <input type="date" name="check_out" min="<?= e($tomorrow->format('Y-m-d')) ?>" value="<?= e($tomorrow->format('Y-m-d')) ?>" required>
                </label>
                <label class="booking-field">
                    <span class="booking-field__label"><i class="fa-regular fa-user" aria-hidden="true"></i> Voyageurs</span>
                    <select name="guests">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= $i === 2 ? 'selected' : '' ?>><?= $i ?> <?= $i > 1 ? 'personnes' : 'personne' ?></option>
                        <?php endfor; ?>
                    </select>
                </label>
                <button class="btn btn--gold booking-bar__submit" type="submit">
                    <span>Préparer ma demande</span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>
            </form>
            <p class="booking-bar__note">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                Disponibilité confirmée par l’équipe après réception de votre demande.
            </p>
        </div>

        <a class="hero__scroll" href="#bienvenue" aria-label="Faire défiler vers la présentation">
            <span>Découvrir</span>
            <span class="hero__scroll-line" aria-hidden="true"></span>
        </a>
    </div>
    <span class="hero__credit">Visuel d’illustration — non contractuel</span>
</section>

<section class="section welcome" id="bienvenue" aria-labelledby="welcome-title">
    <div class="shell welcome__grid">
        <div class="welcome__visual" data-reveal>
            <figure class="welcome__figure welcome__figure--main">
                <?php partial('components/picture', [
                    'path' => 'assets/images/lobby.jpg',
                    'alt' => 'Illustration d’une architecture hôtelière au crépuscule — visuel non contractuel',
                    'sizes' => '(max-width: 900px) 80vw, 38vw',
                    'class' => 'welcome__image',
                ]); ?>
                <figcaption>L’arrivée, au crépuscule <span>· illustration</span></figcaption>
            </figure>
            <figure class="welcome__figure welcome__figure--small">
                <?php partial('components/picture', [
                    'path' => 'assets/images/room-suite.jpg',
                    'alt' => 'Illustration d’une chambre baignée de lumière — visuel non contractuel',
                    'sizes' => '(max-width: 900px) 40vw, 18vw',
                    'class' => 'welcome__image',
                ]); ?>
            </figure>
            <span class="welcome__vertical" aria-hidden="true">Brazza · CG · 4°16′S 15°17′E</span>
        </div>

        <div class="welcome__copy" data-reveal>
            <span class="eyebrow">La maison</span>
            <h2 id="welcome-title" class="display">Une hospitalité<br><em>contemporaine.</em></h2>
            <p class="lead">
                Brazza Hotel cultive une idée simple de l’accueil : celle d’un lieu où
                chaque détail compte, sans jamais s’imposer.
            </p>
            <p>
                Entre la ville et ses rythmes, l’hôtel se présente comme une parenthèse
                chaleureuse à Brazzaville. Les chambres, la table et les services
                officiels seront précisés ici au fur et à mesure de leur validation.
            </p>
            <dl class="welcome__facts">
                <div>
                    <dt>Adresse</dt>
                    <dd>Brazzaville <span>République du Congo</span></dd>
                </div>
                <div>
                    <dt>Catégories</dt>
                    <dd>3 aperçus <span>À confirmer</span></dd>
                </div>
                <div>
                    <dt>Tarifs</dt>
                    <dd>Sur demande <span>Communication officielle</span></dd>
                </div>
            </dl>
            <div class="welcome__actions">
                <a class="text-link" href="<?= e(route('about.index')) ?>">
                    Notre histoire <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
                <span class="demo-note-inline"><span class="demo-chip__dot" aria-hidden="true"></span>Contenu de démonstration</span>
            </div>
        </div>
    </div>
</section>

<section class="section services" aria-labelledby="services-title">
    <div class="shell">
        <div class="section-heading">
            <div>
                <span class="eyebrow">L’expérience</span>
                <h2 id="services-title" class="display">Les attentions<br><em>qui comptent.</em></h2>
            </div>
            <div class="section-heading__aside">
                <p>Un aperçu des services que l’hôtel souhaite mettre en avant. Chaque prestation reste à confirmer avant publication définitive.</p>
                <?php partial('components/demo-chip', ['label' => 'Prestations à confirmer']); ?>
            </div>
        </div>

        <div class="service-grid">
            <?php foreach ($services as $index => $service): ?>
                <article class="service-card" data-reveal>
                    <div class="service-card__top">
                        <span class="service-card__icon"><i class="fa-solid <?= e($service['icon']) ?>" aria-hidden="true"></i></span>
                        <span class="service-card__index">0<?= $index + 1 ?></span>
                    </div>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['description']) ?></p>
                    <?php if (($service['content_status'] ?? 'demo') === 'demo'): ?>
                        <span class="service-card__status">Aperçu éditorial</span>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section rooms-preview" aria-labelledby="rooms-title">
    <div class="shell">
        <div class="section-heading section-heading--light">
            <div>
                <span class="eyebrow">Chambres &amp; suites</span>
                <h2 id="rooms-title" class="display">Des espaces<br><em>à votre image.</em></h2>
            </div>
            <div class="section-heading__aside">
                <p>Trois catégories sont présentées à titre indicatif. Les photographies, équipements et tarifs officiels seront ajoutés après validation.</p>
                <a class="text-link" href="<?= e(route('rooms.index')) ?>">
                    Toutes les chambres <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="room-grid">
            <?php foreach ($rooms as $index => $room): ?>
                <?php $roomUrl = route('rooms.show', ['slug' => $room['slug']]); ?>
                <article class="room-card" data-reveal>
                    <a class="room-card__media" href="<?= e($roomUrl) ?>" aria-label="Découvrir <?= e($room['name']) ?>">
                        <?php partial('components/picture', [
                            'path' => $room['image_path'] ?: 'assets/images/placeholder.svg',
                            'alt' => $room['image_alt'] ?: ('Illustration de ' . $room['name']),
                            'sizes' => '(max-width: 800px) 100vw, 33vw',
                            'class' => 'room-card__image',
                            'width' => 1400,
                            'height' => 933,
                        ]); ?>
                        <span class="room-card__shade"></span>
                        <span class="room-card__number">0<?= $index + 1 ?></span>
                        <?php partial('components/demo-chip', ['label' => 'Aperçu']); ?>
                        <span class="room-card__view">Découvrir <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                    </a>
                    <div class="room-card__body">
                        <div class="room-card__meta">
                            <span><i class="fa-regular fa-user" aria-hidden="true"></i> Jusqu’à <?= (int) $room['max_occupancy'] ?> personnes</span>
                            <?php if ($room['bed_type']): ?>
                                <span><i class="fa-solid fa-bed" aria-hidden="true"></i> <?= e($room['bed_type']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3><a href="<?= e($roomUrl) ?>"><?= e($room['name']) ?></a></h3>
                        <p><?= e($room['short_description']) ?></p>
                        <div class="room-card__foot">
                            <span class="room-card__price"><?= e(price_display($room['price_per_night'] !== null ? (string) $room['price_per_night'] : null, (string) $room['currency'])) ?></span>
                            <a class="round-link" href="<?= e($roomUrl) ?>" aria-label="Voir la fiche de <?= e($room['name']) ?>">
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="restaurant-preview" aria-labelledby="restaurant-title">
    <div class="restaurant-preview__visual" data-reveal>
        <?php partial('components/picture', [
            'path' => 'assets/images/restaurant.jpg',
            'alt' => 'Illustration d’une table gastronomique dressée — visuel non contractuel',
            'sizes' => '(max-width: 900px) 100vw, 52vw',
            'class' => 'restaurant-preview__image',
        ]); ?>
        <div class="restaurant-preview__badge">
            <span>La table</span>
            <em>Brazza</em>
        </div>
        <span class="image-caption">Visuel d’illustration</span>
    </div>
    <div class="restaurant-preview__copy" data-reveal>
        <span class="eyebrow">Restaurant</span>
        <h2 id="restaurant-title" class="display">Une table<br><em>à révéler.</em></h2>
        <p class="lead">
            Le restaurant du Brazza Hotel prépare son identité. Carte, horaires
            et philosophie culinaire seront dévoilés après validation par l’équipe.
        </p>
        <p>
            En attendant, nous vous invitons à découvrir l’esprit du lieu : des
            matières naturelles, une lumière généreuse et le désir de faire de
            chaque repas un moment juste.
        </p>
        <blockquote>
            « Recevoir, c’est offrir du temps. »
        </blockquote>
        <div class="restaurant-preview__foot">
            <a class="btn btn--outline-light" href="<?= e(route('restaurant.index')) ?>">
                Découvrir le restaurant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <span class="demo-note-inline"><span class="demo-chip__dot" aria-hidden="true"></span>Carte à confirmer</span>
        </div>
    </div>
</section>

<section class="section gallery-preview" aria-labelledby="gallery-title">
    <div class="shell">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Galerie</span>
                <h2 id="gallery-title" class="display">Fragments<br><em>de Brazza.</em></h2>
            </div>
            <div class="section-heading__aside">
                <p>Une sélection de visuels d’illustration, en attendant les photographies officielles de l’hôtel.</p>
                <a class="text-link" href="<?= e(route('gallery.index')) ?>">
                    Voir la galerie <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="gallery-grid gallery-grid--home">
            <?php foreach ($gallery as $index => $image): ?>
                <a class="gallery-grid__item <?= e($galleryClasses[$index] ?? '') ?>" href="<?= e(route('gallery.index')) ?>"
                   aria-label="Voir la galerie : <?= e($image['caption'] ?: $image['alt_text']) ?>">
                    <?php partial('components/picture', [
                        'path' => $image['file_path'],
                        'alt' => $image['alt_text'],
                        'sizes' => '(max-width: 700px) 50vw, 33vw',
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
    </div>
</section>

<section class="contact-preview" id="contact" aria-labelledby="contact-title">
    <div class="contact-preview__map">
        <div class="map-fallback" aria-hidden="true">
            <i class="fa-solid fa-location-dot"></i>
            <strong>Brazzaville</strong>
            <span>République du Congo</span>
        </div>
        <iframe
            src="<?= e(map_embed_url()) ?>"
            title="Localisation générale de Brazza Hotel à Brazzaville"
            width="800" height="700" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen></iframe>
        <span class="map-label"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Localisation générale · adresse à confirmer</span>
    </div>

    <div class="contact-preview__panel" data-reveal>
        <span class="eyebrow">Contact</span>
        <h2 id="contact-title" class="display">Parlons de<br><em>votre séjour.</em></h2>
        <p>
            Une question sur les chambres, une demande particulière ou un projet
            de séjour à Brazzaville ? Écrivez-nous, notre équipe vous répondra.
        </p>

        <dl class="contact-list">
            <div>
                <dt><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Adresse</dt>
                <dd>
                    <?= filled(setting('hotel_address')) ? e(setting('hotel_address')) : 'Adresse précise à confirmer' ?><br>
                    Brazzaville, République du Congo
                </dd>
            </div>
            <div>
                <dt><i class="fa-solid fa-phone" aria-hidden="true"></i> Téléphone</dt>
                <dd>
                    <?php if (filled(setting('hotel_phone'))): ?>
                        <a href="<?= e(phone_href(setting('hotel_phone'))) ?>"><?= e(setting('hotel_phone')) ?></a>
                    <?php else: ?>
                        <span class="text-muted">Numéro à confirmer</span>
                    <?php endif; ?>
                </dd>
            </div>
            <div>
                <dt><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</dt>
                <dd>
                    <?php if (whatsapp_href() !== null): ?>
                        <a href="<?= e((string) whatsapp_href()) ?>" target="_blank" rel="noopener noreferrer">Écrire sur WhatsApp</a>
                    <?php else: ?>
                        <span class="text-muted">Numéro WhatsApp à confirmer</span>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>

        <div class="contact-preview__actions">
            <a class="btn btn--gold" href="<?= e(route('contact.index')) ?>">Nous écrire <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            <a class="text-link" href="<?= e(route('reservation.create')) ?>">Réserver un séjour</a>
        </div>
    </div>
</section>
