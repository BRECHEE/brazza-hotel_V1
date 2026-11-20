<?php
$labelFor = static function (string $key): string {
    $label = preg_replace('/^(hotel_|restaurant_|check_)/', '', $key) ?? $key;
    return ucfirst(str_replace('_', ' ', $label));
};
?>
<div class="admin-page-head">
    <div class="admin-page-head__copy">
        <h2>Informations du site</h2>
        <p>Renseignez uniquement les informations confirmées. Une valeur non vérifiée est affichée comme « à confirmer » sur le site.</p>
    </div>
</div>

<div class="admin-note">
    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
    <p><strong>Cochez « Vérifié »</strong> uniquement lorsque l’hôtel a officiellement communiqué la valeur. Les champs vides restent automatiquement marqués comme à confirmer.</p>
</div>

<form action="<?= e(route('admin.settings.update')) ?>" method="post">
    <?= csrf_field() ?>
    <?php foreach ($groupedSettings as $group => $settings): ?>
        <section class="admin-section settings-group">
            <div class="admin-section__head">
                <div>
                    <h2><?= e($groupLabels[$group] ?? ucfirst($group)) ?></h2>
                    <p><?= count($settings) ?> paramètre(s)</p>
                </div>
            </div>
            <div class="admin-section__body">
                <?php foreach ($settings as $setting): ?>
                    <?php
                    $key = (string) $setting['setting_key'];
                    $type = (string) $setting['input_type'];
                    $value = old($key, (string) $setting['setting_value']);
                    $hasError = has_error($key);
                    ?>
                    <div class="setting-row">
                        <div class="setting-row__label">
                            <label for="setting-<?= e($key) ?>"><?= e($labelFor($key)) ?></label>
                            <code><?= e($key) ?></code>
                        </div>
                        <div class="setting-row__input">
                            <?php if ($type === 'textarea'): ?>
                                <textarea id="setting-<?= e($key) ?>" name="<?= e($key) ?>" rows="3" maxlength="5000"><?= $value ?></textarea>
                            <?php elseif ($type === 'time'): ?>
                                <input type="time" id="setting-<?= e($key) ?>" name="<?= e($key) ?>" value="<?= $value ?>">
                            <?php else: ?>
                                <input type="<?= $type === 'phone' ? 'tel' : e($type) ?>" id="setting-<?= e($key) ?>" name="<?= e($key) ?>" value="<?= $value ?>"
                                       maxlength="<?= $type === 'phone' ? '40' : '500' ?>">
                            <?php endif; ?>
                        </div>
                        <div class="setting-row__status">
                            <?php if (!filled((string) $setting['setting_value'])): ?>
                                <span class="unverified-badge">À compléter</span>
                            <?php elseif ((int) $setting['is_verified'] === 1): ?>
                                <span class="verified-badge">Vérifié</span>
                            <?php else: ?>
                                <span class="unverified-badge">À vérifier</span>
                            <?php endif; ?>
                            <label class="checkbox--admin">
                                <input type="checkbox" name="verified[<?= e($key) ?>]" value="1" <?= (int) $setting['is_verified'] === 1 ? 'checked' : '' ?>>
                                Vérifié
                            </label>
                        </div>
                        <?php if ($hasError): ?><span class="field__error"><?= error_for($key) ?></span><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <div class="form-actions">
        <button class="btn btn--gold" type="submit">Enregistrer les informations <i class="fa-solid fa-check" aria-hidden="true"></i></button>
    </div>
</form>
