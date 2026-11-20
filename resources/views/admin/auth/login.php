<?php /* Rendered in layouts/admin-auth. */ ?>
<span class="eyebrow">Espace hôtel</span>
<h1>Connexion<br><em>sécurisée.</em></h1>
<p class="login-card__intro">Accédez à la gestion des demandes, des chambres, de la galerie et des informations du site.</p>

<?php partial('components/alerts'); ?>

<form action="<?= e(route('admin.authenticate')) ?>" method="post" autocomplete="on">
    <?= csrf_field() ?>
    <div class="field <?= has_error('email') ? 'has-error' : '' ?>">
        <label for="email">Adresse e-mail <span class="req">*</span></label>
        <input type="email" id="email" name="email" value="<?= old('email') ?>" required maxlength="190"
               autocomplete="username" autofocus <?= has_error('email') ? 'aria-invalid="true"' : '' ?>>
        <?php if (has_error('email')): ?><span class="field__error"><?= error_for('email') ?></span><?php endif; ?>
    </div>
    <div class="field <?= has_error('password') ? 'has-error' : '' ?>">
        <label for="password">Mot de passe <span class="req">*</span></label>
        <div class="password-field">
            <input type="password" id="password" name="password" required maxlength="200"
                   autocomplete="current-password" <?= has_error('password') ? 'aria-invalid="true"' : '' ?>>
            <button class="password-toggle" type="button" data-password-toggle data-target="password" aria-label="Afficher le mot de passe">
                <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
        </div>
        <?php if (has_error('password')): ?><span class="field__error"><?= error_for('password') ?></span><?php endif; ?>
    </div>
    <button class="btn btn--gold btn--block" type="submit">Se connecter <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
</form>

<div class="login-card__footer">
    <a href="<?= e(route('home')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour au site</a>
    <span>Accès protégé · sessions chiffrées</span>
</div>
