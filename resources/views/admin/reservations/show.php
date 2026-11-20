<?php
$nights = (new DateTimeImmutable((string) $reservation['check_in']))->diff(new DateTimeImmutable((string) $reservation['check_out']))->days;
$whatsappPhone = preg_replace('/\D+/', '', (string) $reservation['phone']);
?>
<a class="admin-back" href="<?= e(route('admin.reservations.index')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Toutes les demandes</a>

<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2><?= e($reservation['guest_name']) ?></h2>
        <p>Référence <span class="cell-mono"><?= e($reservation['reference']) ?></span> · reçue le <?= e(format_datetime_fr((string) $reservation['created_at'])) ?></p>
    </div>
    <div class="admin-page-head__actions">
        <span class="<?= e(reservation_status_class((string) $reservation['status'])) ?>"><?= e(status_label((string) $reservation['status'])) ?></span>
    </div>
</div>

<div class="admin-grid admin-grid--sidebar">
    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Détails du séjour</h2>
            </div>
            <dl class="detail-grid">
                <div class="detail-item">
                    <dt>Arrivée</dt>
                    <dd><?= e(format_date_fr((string) $reservation['check_in'])) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Départ</dt>
                    <dd><?= e(format_date_fr((string) $reservation['check_out'])) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Nuits</dt>
                    <dd><?= (int) $nights ?> nuit<?= $nights > 1 ? 's' : '' ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Voyageurs</dt>
                    <dd><?= (int) $reservation['guests'] ?> personne<?= (int) $reservation['guests'] > 1 ? 's' : '' ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Catégorie demandée</dt>
                    <dd><?= e($reservation['room_name'] ?: 'Catégorie supprimée') ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Canal</dt>
                    <dd><?= e(ucfirst((string) $reservation['source'])) ?></dd>
                </div>
            </dl>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Coordonnées</h2>
            </div>
            <dl class="detail-grid">
                <div class="detail-item">
                    <dt>Nom complet</dt>
                    <dd><?= e($reservation['guest_name']) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Téléphone</dt>
                    <dd><a href="tel:<?= e((string) $reservation['phone']) ?>"><?= e($reservation['phone']) ?></a></dd>
                </div>
                <div class="detail-item">
                    <dt>E-mail</dt>
                    <dd>
                        <?php if (filled($reservation['email'])): ?>
                            <a href="mailto:<?= e($reservation['email']) ?>"><?= e($reservation['email']) ?></a>
                        <?php else: ?>
                            <span class="admin-muted">Non fourni</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="detail-item">
                    <dt>WhatsApp</dt>
                    <dd>
                        <?php if ($whatsappPhone !== ''): ?>
                            <a href="https://wa.me/<?= e($whatsappPhone) ?>" target="_blank" rel="noopener noreferrer">Ouvrir WhatsApp</a>
                        <?php else: ?>
                            <span class="admin-muted">Numéro invalide</span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Message du voyageur</h2>
            </div>
            <div class="admin-section__body">
                <?php if (filled($reservation['message'])): ?>
                    <div class="detail-message"><?= nl2br(e($reservation['message'])) ?></div>
                <?php else: ?>
                    <p class="admin-muted">Aucun message complémentaire.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <div>
                    <h2>Suivi de la demande</h2>
                    <p>La confirmation reste une décision humaine.</p>
                </div>
            </div>
            <div class="admin-section__body">
                <form action="<?= e(route('admin.reservations.status', ['id' => (int) $reservation['id']])) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="reservation-status">Statut</label>
                        <select id="reservation-status" name="status">
                            <?php foreach (['new' => 'Nouvelle', 'contacted' => 'Contactée', 'confirmed' => 'Confirmée', 'declined' => 'Refusée', 'cancelled' => 'Annulée'] as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $reservation['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-top: 0.8rem;">
                        <label for="admin-notes">Notes internes</label>
                        <textarea id="admin-notes" name="admin_notes" rows="5" maxlength="5000"
                                  placeholder="Échanges, tarif communiqué, proposition alternative…"><?= e($reservation['admin_notes'] ?? '') ?></textarea>
                        <span class="admin-help">Ces notes ne sont jamais affichées sur le site public.</span>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn--gold btn--small" type="submit">Enregistrer <i class="fa-solid fa-check" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Rappel</h2>
            </div>
            <div class="admin-section__body">
                <div class="admin-note">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    <p><strong>Ne promettez pas de disponibilité</strong> avant vérification auprès de l’hôtel. Le site indique explicitement qu’une demande n’est pas une réservation confirmée.</p>
                </div>
                <p class="admin-muted">Le voyageur a été informé que cette demande ne bloque aucune chambre et qu’aucun paiement n’est effectué en ligne.</p>
            </div>
        </section>
    </div>
</div>
