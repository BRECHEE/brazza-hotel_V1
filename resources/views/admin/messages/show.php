<?php ?>
<a class="admin-back" href="<?= e(route('admin.messages.index')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Tous les messages</a>

<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2><?= e($message['subject']) ?></h2>
        <p>De <?= e($message['name']) ?> · reçu le <?= e(format_datetime_fr((string) $message['created_at'])) ?></p>
    </div>
    <div class="admin-page-head__actions">
        <span class="<?= e(reservation_status_class((string) $message['status'])) ?>"><?= e(status_label((string) $message['status'])) ?></span>
    </div>
</div>

<div class="admin-grid admin-grid--sidebar">
    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Message</h2>
            </div>
            <div class="admin-section__body">
                <div class="detail-message"><?= nl2br(e($message['message'])) ?></div>
            </div>
        </section>

        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Coordonnées</h2>
            </div>
            <dl class="detail-grid">
                <div class="detail-item">
                    <dt>Nom</dt>
                    <dd><?= e($message['name']) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>E-mail</dt>
                    <dd><a href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . $message['subject']) ?>"><?= e($message['email']) ?></a></dd>
                </div>
                <div class="detail-item">
                    <dt>Téléphone</dt>
                    <dd>
                        <?php if (filled($message['phone'])): ?>
                            <a href="tel:<?= e((string) $message['phone']) ?>"><?= e($message['phone']) ?></a>
                        <?php else: ?>
                            <span class="admin-muted">Non fourni</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="detail-item">
                    <dt>Reçu le</dt>
                    <dd><?= e(format_datetime_fr((string) $message['created_at'])) ?></dd>
                </div>
            </dl>
        </section>
    </div>

    <div class="admin-stack">
        <section class="admin-section">
            <div class="admin-section__head">
                <h2>Suivi</h2>
            </div>
            <div class="admin-section__body">
                <form action="<?= e(route('admin.messages.status', ['id' => (int) $message['id']])) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="message-status">Statut</label>
                        <select id="message-status" name="status">
                            <?php foreach (['new' => 'Nouveau', 'read' => 'Lu', 'replied' => 'Répondu', 'archived' => 'Archivé'] as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $message['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn--gold btn--small" type="submit">Enregistrer</button>
                    </div>
                </form>
            </div>
        </section>
        <a class="btn btn--ghost btn--small btn--block" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . $message['subject']) ?>">
            <i class="fa-solid fa-reply" aria-hidden="true"></i> Répondre par e-mail
        </a>
    </div>
</div>
