<?php
partial('components/page-hero', [
    'kicker' => 'Demande enregistrée',
    'title' => 'Merci pour<br><em>votre confiance.</em>',
    'intro' => 'Votre demande a bien été transmise. Elle reste soumise à la vérification et à la confirmation de l’hôtel.',
    'image' => 'assets/images/gallery-pool.jpg',
    'imageAlt' => 'Illustration d’un espace extérieur avec piscine — visuel non contractuel',
    'aside' => 'Demande non confirmée<br>Réponse à venir',
]);
?>
<section class="section reservation-page">
    <div class="shell">
        <div class="confirmation">
            <span class="confirmation__icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
            <span class="eyebrow eyebrow--plain" style="justify-content: center;">Demande reçue</span>
            <h1>Votre demande<br><em>est enregistrée.</em></h1>
            <span class="confirmation__ref">Référence : <?= e($reference) ?></span>
            <p>Un membre de l’équipe du Brazza Hotel examinera votre demande et vous contactera aux coordonnées fournies. Aucune disponibilité ni aucun tarif ne sont confirmés à ce stade.</p>
            <div class="notice" style="max-width: 600px; margin: 1.8rem auto 0; text-align: left;">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                <p><strong>Conservez votre référence.</strong> Elle facilitera nos échanges. Si vous ne recevez pas de réponse dans un délai raisonnable, contactez-nous directement.</p>
            </div>
            <div class="confirmation__actions">
                <?php if ($whatsappUrl !== null): ?>
                    <a class="btn btn--gold" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Confirmer sur WhatsApp
                    </a>
                <?php endif; ?>
                <a class="btn btn--ghost" href="<?= e(route('home')) ?>">Retour à l’accueil</a>
                <a class="btn btn--ghost" href="<?= e(route('contact.index')) ?>">Nous contacter</a>
            </div>
            <p class="confirmation__notice">Page privée · ne partagez pas votre référence publiquement.</p>
        </div>
    </div>
</section>
