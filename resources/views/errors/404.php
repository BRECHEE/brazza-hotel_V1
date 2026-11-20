<?php $message = $message ?? 'La page demandée n’existe pas ou a été déplacée.'; ?>
<section class="section" style="min-height: 78vh; display: flex; align-items: center;">
    <div class="shell">
        <div class="confirmation">
            <span class="error-code">404</span>
            <h1>Page<br><em>introuvable.</em></h1>
            <p><?= e($message) ?></p>
            <div class="confirmation__actions">
                <a class="btn btn--gold" href="<?= e(route('home')) ?>">Retour à l’accueil</a>
                <a class="btn btn--ghost" href="<?= e(route('rooms.index')) ?>">Voir les chambres</a>
            </div>
        </div>
    </div>
</section>
