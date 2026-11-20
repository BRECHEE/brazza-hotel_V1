<?php ?>
<a class="admin-back" href="<?= e(route('admin.dashboard')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Tableau de bord</a>

<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Mon compte</h2>
        <p>Gérez votre identité et sécurisez l’accès à l’administration.</p>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <section class="admin-form-card">
        <div class="admin-form-card__head">
            <h2>Profil</h2>
        </div>
        <div class="admin-form-card__body">
            <form action="<?= e(route('admin.profile.update')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="field <?= has_error('name') ? 'has-error' : '' ?>">
                    <label for="profile-name">Nom complet</label>
                    <input type="text" id="profile-name" name="name" value="<?= old('name', (string) ($profile['name'] ?? '')) ?>" required minlength="2" maxlength="120">
                    <?php if (has_error('name')): ?><span class="field__error"><?= error_for('name') ?></span><?php endif; ?>
                </div>
                <div class="field" style="margin-top:0.9rem;">
                    <label for="profile-email">Adresse e-mail</label>
                    <input type="email" id="profile-email" name="email" value="<?= old('email', (string) ($profile['email'] ?? '')) ?>" required maxlength="190">
                    <?php if (has_error('email')): ?><span class="field__error"><?= error_for('email') ?></span><?php endif; ?>
                </div>
                <div class="form-actions">
                    <button class="btn btn--gold btn--small" type="submit">Mettre à jour</button>
                </div>
            </form>
        </div>
    </section>

    <section class="admin-form-card">
        <div class="admin-form-card__head">
            <h2>Compte</h2>
        </div>
        <div class="admin-form-card__body">
            <dl class="detail-grid">
                <div class="detail-item">
                    <dt>Rôle</dt>
                    <dd><?= e(ucfirst((string) ($profile['role'] ?? 'manager'))) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Dernière connexion</dt>
                    <dd><?= e(format_datetime_fr($profile['last_login_at'] ?? null)) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Compte créé le</dt>
                    <dd><?= e(format_date_fr(substr((string) ($profile['created_at'] ?? ''), 0, 10))) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Session</dt>
                    <dd>Sécurisée · expiration après inactivité</dd>
                </div>
            </dl>
        </div>
    </section>
</div>

<section class="admin-form-card" style="margin-top:1.2rem;">
    <div class="admin-form-card__head">
        <h2>Changer le mot de passe</h2>
    </div>
    <div class="admin-form-card__body">
        <form action="<?= e(route('admin.profile.password')) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="field field--full <?= has_error('current_password') ? 'has-error' : '' ?>">
                    <label for="current-password">Mot de passe actuel</label>
                    <input type="password" id="current-password" name="current_password" required autocomplete="current-password">
                    <?php if (has_error('current_password')): ?><span class="field__error"><?= error_for('current_password') ?></span><?php endif; ?>
                </div>
                <div class="field <?= has_error('new_password') ? 'has-error' : '' ?>">
                    <label for="new-password">Nouveau mot de passe</label>
                    <input type="password" id="new-password" name="new_password" required minlength="12" autocomplete="new-password">
                    <span class="admin-help">12 caractères minimum. Utilisez une phrase de passe unique.</span>
                    <?php if (has_error('new_password')): ?><span class="field__error"><?= error_for('new_password') ?></span><?php endif; ?>
                </div>
                <div class="field <?= has_error('confirm_password') ? 'has-error' : '' ?>">
                    <label for="confirm-password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm_password" required minlength="12" autocomplete="new-password">
                    <?php if (has_error('confirm_password')): ?><span class="field__error"><?= error_for('confirm_password') ?></span><?php endif; ?>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn--gold btn--small" type="submit">Modifier le mot de passe</button>
            </div>
        </form>
    </div>
</section>
