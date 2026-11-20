<?php
$ctaTitle = $ctaTitle ?? 'Votre séjour commence<br>par un <em>message.</em>';
$ctaText = $ctaText ?? 'Partagez-nous vos dates et vos envies. L’équipe du Brazza Hotel vous répondra personnellement.';
?>
<section class="cta-band" aria-labelledby="cta-title">
    <div class="cta-band__glow" aria-hidden="true"></div>
    <div class="shell cta-band__inner">
        <div>
            <span class="eyebrow">Réservation</span>
            <h2 id="cta-title" class="display"><?= $ctaTitle ?></h2>
        </div>
        <div class="cta-band__aside">
            <p><?= e($ctaText) ?></p>
            <div class="cta-band__actions">
                <a class="btn btn--gold" href="<?= e(route('reservation.create')) ?>">
                    Demander une chambre <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
                <?php if (whatsapp_href() !== null): ?>
                    <a class="btn btn--ghost" href="<?= e((string) whatsapp_href('Bonjour Brazza Hotel, je souhaite préparer un séjour.')) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
                    </a>
                <?php else: ?>
                    <a class="btn btn--ghost" href="<?= e(route('contact.index')) ?>">Nous contacter</a>
                <?php endif; ?>
            </div>
            <p class="cta-band__note">Demande de réservation · disponibilité confirmée par l’hôtel</p>
        </div>
    </div>
</section>
