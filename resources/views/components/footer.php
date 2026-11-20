<?php
/** @var string $phone @var string $whatsapp */
$email = setting('hotel_email', (string) config('app.hotel.email'));
$address = setting('hotel_address', (string) config('app.hotel.address'));
$instagram = setting('instagram_url');
$facebook = setting('facebook_url');
?>
<footer class="site-footer">
    <div class="shell">
        <div class="footer-statement">
            <span class="eyebrow eyebrow--plain">Brazzaville · République du Congo</span>
            <p class="footer-statement__line">L’art de recevoir, <em>simplement.</em></p>
        </div>

        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand brand--footer" href="<?= e(route('home')) ?>" aria-label="Brazza Hotel — Accueil">
                    <img class="brand__mark" src="<?= e(asset('assets/images/monogram.svg')) ?>" alt="" width="48" height="48">
                    <span class="brand__type">
                        <span class="brand__name">Brazza</span>
                        <span class="brand__sub">Hôtel · Brazzaville</span>
                    </span>
                </a>
                <p>Une adresse contemporaine au cœur de Brazzaville, pensée pour les voyageurs qui recherchent calme, attention et élégance.</p>
                <p class="footer-demo-note">Site en cours de validation éditoriale. Les informations officielles, tarifs et photographies seront publiés après confirmation par l’hôtel.</p>
                <div class="social-links">
                    <?php if (filled($instagram)): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                    <?php endif; ?>
                    <?php if (filled($facebook)): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    <?php endif; ?>
                    <?php if (whatsapp_href() !== null): ?>
                        <a href="<?= e((string) whatsapp_href()) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-col">
                <h2 class="footer-heading">Explorer</h2>
                <ul>
                    <li><a href="<?= e(route('rooms.index')) ?>">Chambres</a></li>
                    <li><a href="<?= e(route('restaurant.index')) ?>">Restaurant</a></li>
                    <li><a href="<?= e(route('gallery.index')) ?>">Galerie</a></li>
                    <li><a href="<?= e(route('about.index')) ?>">À propos</a></li>
                    <li><a href="<?= e(route('contact.index')) ?>">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h2 class="footer-heading">Séjour</h2>
                <ul>
                    <li><a href="<?= e(route('reservation.create')) ?>">Demande de réservation</a></li>
                    <li><a href="<?= e(route('contact.index')) ?>#acces">Accès &amp; localisation</a></li>
                    <li><a href="<?= e(route('legal.privacy')) ?>">Données personnelles</a></li>
                    <li><a href="<?= e(route('legal.mentions')) ?>">Mentions légales</a></li>
                    <li><a href="<?= e(route('admin.login')) ?>">Espace hôtel</a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact">
                <h2 class="footer-heading">Nous joindre</h2>
                <address>
                    <span><?= filled($address) ? e($address) : 'Adresse précise à confirmer' ?></span>
                    <span>Brazzaville, République du Congo</span>
                </address>
                <?php if (filled($phone)): ?>
                    <a href="<?= e(phone_href($phone)) ?>"><?= e($phone) ?></a>
                <?php else: ?>
                    <span class="footer-placeholder">Téléphone à confirmer</span>
                <?php endif; ?>
                <?php if (filled($email)): ?>
                    <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                <?php else: ?>
                    <span class="footer-placeholder">E-mail à confirmer</span>
                <?php endif; ?>
                <?php if (whatsapp_href() !== null): ?>
                    <a class="footer-whatsapp" href="<?= e((string) whatsapp_href()) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
                    </a>
                <?php else: ?>
                    <span class="footer-placeholder"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp à confirmer</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <?= e(current_year()) ?> Brazza Hotel. Tous droits réservés.</p>
            <p class="footer-credit">Photographies d’illustration non contractuelles · <a href="https://unsplash.com" target="_blank" rel="noopener noreferrer">Unsplash</a></p>
            <a class="back-to-top" href="#main">
                Haut de page <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</footer>
