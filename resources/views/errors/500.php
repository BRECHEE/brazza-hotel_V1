<span class="error-code">500</span>
<h1 class="error-title">Service momentanément indisponible</h1>
<p class="error-copy">Une erreur technique est survenue. Merci de réessayer dans quelques instants. L’incident a été enregistré.</p>
<?php if (!empty($debugMessage)): ?>
    <pre class="debug-detail"><?= e((string) $debugMessage) ?></pre>
<?php endif; ?>
<div class="error-actions">
    <a class="btn btn--gold" href="<?= e(route('home')) ?>">Retour à l’accueil</a>
</div>
