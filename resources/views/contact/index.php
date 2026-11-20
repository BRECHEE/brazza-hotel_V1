<?php
$phone = setting('hotel_phone');
$whatsapp = setting('hotel_whatsapp');
$email = setting('hotel_email');
$address = setting('hotel_address');
partial('components/page-hero', [
    'kicker' => 'Contact',
    'title' => 'Écrivons la suite<br><em>de votre séjour.</em>',
    'intro' => 'Une question, une demande particulière, un projet de séjour ? Laissez-nous un message : nous vous répondrons avec attention.',
    'image' => 'assets/images/gallery-lounge.jpg',
    'imageAlt' => 'Illustration d’un extérieur d’hôtel — visuel non contractuel',
    'aside' => 'Coordonnées officielles<br>à confirmer',
]);
?>
<section class="section contact-page">
    <div class="shell">
        <?php partial('components/alerts'); ?>

        <div class="contact-layout">
            <div>
                <span class="eyebrow">Nous joindre</span>
                <h2 class="display">À votre<br><em>écoute.</em></h2>
                <p class="lead" style="margin-top: 1.5rem;">Les coordonnées officielles de l’hôtel sont en cours de validation. En attendant, le formulaire nous permet de recevoir votre demande.</p>

                <dl class="contact-details" style="margin-top: 2rem;">
                    <div class="contact-detail">
                        <dt><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Adresse</dt>
                        <dd>
                            <?= filled($address) ? e($address) : 'Adresse précise à confirmer' ?><br>
                            Brazzaville, République du Congo
                        </dd>
                    </div>
                    <div class="contact-detail">
                        <dt><i class="fa-solid fa-phone" aria-hidden="true"></i> Téléphone</dt>
                        <dd>
                            <?php if (filled($phone)): ?>
                                <a href="<?= e(phone_href($phone)) ?>"><?= e($phone) ?></a>
                            <?php else: ?>
                                <span class="text-muted">Numéro officiel à confirmer</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="contact-detail">
                        <dt><i class="fa-solid fa-envelope" aria-hidden="true"></i> E-mail</dt>
                        <dd>
                            <?php if (filled($email)): ?>
                                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                            <?php else: ?>
                                <span class="text-muted">Adresse e-mail à confirmer</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="contact-detail" id="whatsapp">
                        <dt><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</dt>
                        <dd>
                            <?php if (whatsapp_href() !== null): ?>
                                <a href="<?= e((string) whatsapp_href()) ?>" target="_blank" rel="noopener noreferrer">Écrire sur WhatsApp</a>
                            <?php else: ?>
                                <span class="text-muted">Numéro WhatsApp officiel à confirmer</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="contact-detail">
                        <dt><i class="fa-regular fa-clock" aria-hidden="true"></i> Horaires</dt>
                        <dd><span class="text-muted">Horaires de réception à confirmer</span></dd>
                    </div>
                </dl>

                <div class="contact-actions">
                    <a class="btn btn--gold" href="<?= e(route('reservation.create')) ?>">Demande de réservation</a>
                    <?php if (filled($phone)): ?>
                        <a class="btn btn--ghost" href="<?= e(phone_href($phone)) ?>">Appeler</a>
                    <?php endif; ?>
                </div>

                <?php if (whatsapp_href() !== null): ?>
                    <div class="whatsapp-panel">
                        <div>
                            <h3><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</h3>
                            <p>Pour une réponse rapide, écrivez-nous directement.</p>
                        </div>
                        <a class="btn btn--gold btn--small" href="<?= e((string) whatsapp_href('Bonjour Brazza Hotel, je souhaite obtenir des informations.')) ?>" target="_blank" rel="noopener noreferrer">Ouvrir WhatsApp</a>
                    </div>
                <?php else: ?>
                    <div class="notice" style="margin-top: 1.5rem;">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                        <p><strong>WhatsApp bientôt disponible.</strong> Le numéro officiel sera publié ici après confirmation par l’hôtel.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="contact-form-card" id="formulaire">
                <span class="eyebrow">Formulaire</span>
                <h2>Un message,<br><em>une réponse.</em></h2>
                <p>Les champs marqués d’un astérisque sont obligatoires. Votre message est enregistré de façon sécurisée.</p>

                <form action="<?= e(route('contact.store')) ?>" method="post" autocomplete="on">
                    <?= csrf_field() ?>
                    <div class="honeypot" aria-hidden="true">
                        <label for="contact-company">Société</label>
                        <input type="text" id="contact-company" name="company" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-grid">
                        <div class="field <?= has_error('name') ? 'has-error' : '' ?>">
                            <label for="contact-name">Nom complet <span class="req">*</span></label>
                            <input type="text" id="contact-name" name="name" value="<?= old('name') ?>" required minlength="2" maxlength="120" autocomplete="name"
                                   <?= has_error('name') ? 'aria-invalid="true" aria-describedby="error-name"' : '' ?>>
                            <?php if (has_error('name')): ?><span class="field__error" id="error-name"><?= error_for('name') ?></span><?php endif; ?>
                        </div>
                        <div class="field <?= has_error('email') ? 'has-error' : '' ?>">
                            <label for="contact-email">E-mail <span class="req">*</span></label>
                            <input type="email" id="contact-email" name="email" value="<?= old('email') ?>" required maxlength="190" autocomplete="email"
                                   <?= has_error('email') ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>>
                            <?php if (has_error('email')): ?><span class="field__error" id="error-email"><?= error_for('email') ?></span><?php endif; ?>
                        </div>
                        <div class="field <?= has_error('phone') ? 'has-error' : '' ?>">
                            <label for="contact-phone">Téléphone</label>
                            <input type="tel" id="contact-phone" name="phone" value="<?= old('phone') ?>" maxlength="40" autocomplete="tel"
                                   placeholder="+242 …">
                            <?php if (has_error('phone')): ?><span class="field__error"><?= error_for('phone') ?></span><?php endif; ?>
                        </div>
                        <div class="field <?= has_error('subject') ? 'has-error' : '' ?>">
                            <label for="contact-subject">Sujet <span class="req">*</span></label>
                            <input type="text" id="contact-subject" name="subject" value="<?= old('subject') ?>" required minlength="2" maxlength="160"
                                   <?= has_error('subject') ? 'aria-invalid="true"' : '' ?>>
                            <?php if (has_error('subject')): ?><span class="field__error"><?= error_for('subject') ?></span><?php endif; ?>
                        </div>
                        <div class="field field--full <?= has_error('message') ? 'has-error' : '' ?>">
                            <label for="contact-message">Votre message <span class="req">*</span></label>
                            <textarea id="contact-message" name="message" required minlength="10" maxlength="4000" rows="6"
                                      placeholder="Dites-nous comment nous pouvons vous aider…"
                                      <?= has_error('message') ? 'aria-invalid="true"' : '' ?>><?= old('message') ?></textarea>
                            <?php if (has_error('message')): ?><span class="field__error"><?= error_for('message') ?></span><?php endif; ?>
                        </div>
                        <div class="field field--full <?= has_error('consent') ? 'has-error' : '' ?>">
                            <label class="checkbox" for="contact-consent">
                                <input type="checkbox" id="contact-consent" name="consent" value="1" required <?= old('consent') === '1' ? 'checked' : '' ?>>
                                <span>J’accepte que mes données soient utilisées pour traiter ma demande, conformément à la <a href="<?= e(route('legal.privacy')) ?>">politique de confidentialité</a>. <span class="req">*</span></span>
                            </label>
                            <?php if (has_error('consent')): ?><span class="field__error"><?= error_for('consent') ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-foot">
                        <p>Ce formulaire ne constitue pas une réservation. Les champs sont vérifiés côté serveur.</p>
                        <button class="btn btn--gold" type="submit">Envoyer le message <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="contact-map" id="acces">
            <div class="map-fallback" aria-hidden="true">
                <i class="fa-solid fa-location-dot"></i>
                <strong>Brazzaville</strong>
                <span>République du Congo · adresse à confirmer</span>
            </div>
            <iframe src="<?= e(map_embed_url()) ?>" title="Localisation générale de Brazza Hotel à Brazzaville"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            <span class="map-label"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Localisation générale · adresse exacte à confirmer</span>
        </div>

        <div class="notice" style="margin-top: 1.5rem;">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <p><strong>Adresse et coordonnées.</strong> Seule la localisation générale à Brazzaville est confirmée. Aucune adresse, aucun numéro et aucun horaire officiels ne sont publiés tant qu’ils n’ont pas été transmis par l’hôtel.</p>
        </div>
    </div>
</section>
