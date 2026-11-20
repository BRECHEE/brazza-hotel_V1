<?php
$selectedRoomValue = (int) old('room_id', (string) $selectedRoom);
$checkInValue = old('check_in', $checkIn);
$checkOutValue = old('check_out', $checkOut);
$guestsValue = (int) old('guests', (string) $guests);
$guestsValue = max(1, min(10, $guestsValue));
partial('components/page-hero', [
    'kicker' => 'Réservation',
    'title' => 'Demander<br><em>un séjour.</em>',
    'intro' => 'Partagez vos dates et vos coordonnées. Votre demande sera étudiée personnellement par l’équipe du Brazza Hotel.',
    'image' => 'assets/images/room-superior.jpg',
    'imageAlt' => 'Illustration d’une chambre d’hôtel — visuel non contractuel',
    'aside' => 'Demande sans engagement<br>Réponse de l’équipe',
]);
?>
<section class="section reservation-page" id="demande">
    <div class="shell">
        <div class="intro-row" style="margin-bottom: clamp(2.5rem, 5vw, 4rem);">
            <div class="intro-row__title">
                <span class="eyebrow">Votre demande</span>
                <h2 class="display">Quelques détails,<br><em>et nous prenons le relais.</em></h2>
            </div>
            <div class="intro-row__copy">
                <p class="lead">Ce formulaire enregistre une demande. Il ne bloque pas encore de chambre et ne confirme aucune disponibilité.</p>
                <p>Notre équipe vérifie les possibilités auprès de l’hôtel puis vous contacte par téléphone ou par e-mail. Aucun paiement n’est demandé sur ce site.</p>
            </div>
        </div>

        <?php partial('components/alerts'); ?>

        <div class="reservation-layout">
            <div class="reservation-form">
                <span class="eyebrow">Formulaire sécurisé</span>
                <h2>Parlez-nous de<br><em>votre séjour.</em></h2>
                <p class="text-muted" style="margin-top: 0.8rem; font-size: 0.8rem;">Les champs marqués d’un astérisque sont obligatoires.</p>

                <?php if ($rooms === []): ?>
                    <div class="notice" style="margin-top: 1.5rem;">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <p>Les catégories de chambres ne sont pas disponibles pour le moment. Merci de nous contacter directement.</p>
                    </div>
                <?php else: ?>
                    <form action="<?= e(route('reservation.store')) ?>" method="post" autocomplete="on">
                        <?= csrf_field() ?>
                        <div class="honeypot" aria-hidden="true">
                            <label for="reservation-company">Société</label>
                            <input type="text" id="reservation-company" name="company" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-grid">
                            <div class="field field--full <?= has_error('room_id') ? 'has-error' : '' ?>">
                                <label for="reservation-room">Catégorie de chambre <span class="req">*</span></label>
                                <select id="reservation-room" name="room_id" required
                                        <?= has_error('room_id') ? 'aria-invalid="true"' : '' ?>>
                                    <option value="">Choisir une catégorie</option>
                                    <?php foreach ($rooms as $roomOption): ?>
                                        <option value="<?= (int) $roomOption['id'] ?>" <?= $selectedRoomValue === (int) $roomOption['id'] ? 'selected' : '' ?>>
                                            <?= e($roomOption['name']) ?> — jusqu’à <?= (int) $roomOption['max_occupancy'] ?> personnes (indicatif)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (has_error('room_id')): ?><span class="field__error"><?= error_for('room_id') ?></span><?php endif; ?>
                            </div>

                            <div class="field <?= has_error('check_in') ? 'has-error' : '' ?>">
                                <label for="reservation-checkin">Arrivée <span class="req">*</span></label>
                                <input type="date" id="reservation-checkin" name="check_in" value="<?= e($checkInValue) ?>" min="<?= e($today) ?>" required
                                       <?= has_error('check_in') ? 'aria-invalid="true"' : '' ?>>
                                <?php if (has_error('check_in')): ?><span class="field__error"><?= error_for('check_in') ?></span><?php endif; ?>
                            </div>
                            <div class="field <?= has_error('check_out') ? 'has-error' : '' ?>">
                                <label for="reservation-checkout">Départ <span class="req">*</span></label>
                                <input type="date" id="reservation-checkout" name="check_out" value="<?= e($checkOutValue) ?>" min="<?= e($tomorrow) ?>" required
                                       <?= has_error('check_out') ? 'aria-invalid="true"' : '' ?>>
                                <?php if (has_error('check_out')): ?><span class="field__error"><?= error_for('check_out') ?></span><?php endif; ?>
                            </div>

                            <div class="field <?= has_error('guests') ? 'has-error' : '' ?>">
                                <label for="reservation-guests">Voyageurs <span class="req">*</span></label>
                                <select id="reservation-guests" name="guests" required>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?= $i ?>" <?= $guestsValue === $i ? 'selected' : '' ?>><?= $i ?> <?= $i > 1 ? 'personnes' : 'personne' ?></option>
                                    <?php endfor; ?>
                                </select>
                                <?php if (has_error('guests')): ?><span class="field__error"><?= error_for('guests') ?></span><?php endif; ?>
                            </div>
                            <div class="field <?= has_error('guest_name') ? 'has-error' : '' ?>">
                                <label for="reservation-name">Nom complet <span class="req">*</span></label>
                                <input type="text" id="reservation-name" name="guest_name" value="<?= old('guest_name') ?>" required minlength="2" maxlength="120" autocomplete="name"
                                       <?= has_error('guest_name') ? 'aria-invalid="true"' : '' ?>>
                                <?php if (has_error('guest_name')): ?><span class="field__error"><?= error_for('guest_name') ?></span><?php endif; ?>
                            </div>

                            <div class="field <?= has_error('phone') ? 'has-error' : '' ?>">
                                <label for="reservation-phone">Téléphone <span class="req">*</span></label>
                                <input type="tel" id="reservation-phone" name="phone" value="<?= old('phone') ?>" required maxlength="40" autocomplete="tel"
                                       placeholder="+242 …" <?= has_error('phone') ? 'aria-invalid="true"' : '' ?>>
                                <?php if (has_error('phone')): ?><span class="field__error"><?= error_for('phone') ?></span><?php endif; ?>
                            </div>
                            <div class="field <?= has_error('email') ? 'has-error' : '' ?>">
                                <label for="reservation-email">E-mail</label>
                                <input type="email" id="reservation-email" name="email" value="<?= old('email') ?>" maxlength="190" autocomplete="email"
                                       <?= has_error('email') ? 'aria-invalid="true"' : '' ?>>
                                <?php if (has_error('email')): ?><span class="field__error"><?= error_for('email') ?></span><?php endif; ?>
                            </div>

                            <div class="field field--full <?= has_error('message') ? 'has-error' : '' ?>">
                                <label for="reservation-message">Votre message</label>
                                <textarea id="reservation-message" name="message" rows="5" maxlength="2000"
                                          placeholder="Heure d’arrivée estimée, demande particulière, occasion…"><?= old('message') ?></textarea>
                                <?php if (has_error('message')): ?><span class="field__error"><?= error_for('message') ?></span><?php endif; ?>
                            </div>

                            <div class="field field--full <?= has_error('consent') ? 'has-error' : '' ?>">
                                <label class="checkbox" for="reservation-consent">
                                    <input type="checkbox" id="reservation-consent" name="consent" value="1" required <?= old('consent') === '1' ? 'checked' : '' ?>>
                                    <span>J’accepte que mes informations soient utilisées pour traiter ma demande de réservation, conformément à la <a href="<?= e(route('legal.privacy')) ?>">politique de confidentialité</a>. <span class="req">*</span></span>
                                </label>
                                <?php if (has_error('consent')): ?><span class="field__error"><?= error_for('consent') ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-foot">
                            <p>En envoyant ce formulaire, vous comprenez qu’il s’agit d’une demande et non d’une confirmation de réservation.</p>
                            <button class="btn btn--gold" type="submit">Envoyer ma demande <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <aside class="reservation-aside">
                <div class="reservation-aside__card">
                    <span class="eyebrow">Comment ça marche</span>
                    <h2>Une demande,<br><em>un échange.</em></h2>
                    <p>Nous privilégions le contact humain : chaque demande est lue et traitée par l’équipe de l’hôtel.</p>
                    <ol class="process-list">
                        <li><span>01</span>Vous envoyez vos dates et vos coordonnées.</li>
                        <li><span>02</span>L’équipe vérifie la disponibilité et les conditions.</li>
                        <li><span>03</span>Nous vous contactons pour confirmer ou proposer une alternative.</li>
                    </ol>
                    <div class="notice">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <p>Aucun paiement n’est demandé et aucune chambre n’est bloquée avant confirmation.</p>
                    </div>
                    <div class="reservation-aside__contact">
                        <p style="margin-bottom: 0.45rem;">Une question urgente ?</p>
                        <?php if (filled(setting('hotel_phone'))): ?>
                            <a href="<?= e(phone_href(setting('hotel_phone'))) ?>"><?= e(setting('hotel_phone')) ?></a>
                        <?php else: ?>
                            <span class="text-muted">Téléphone à confirmer — utilisez le formulaire de contact.</span>
                        <?php endif; ?>
                        <?php if (whatsapp_href() !== null): ?>
                            <p style="margin-top: 0.5rem;"><a href="<?= e((string) whatsapp_href('Bonjour Brazza Hotel, je prépare une demande de réservation.')) ?>" target="_blank" rel="noopener noreferrer">WhatsApp <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
