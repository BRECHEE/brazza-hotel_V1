<?php
partial('components/page-hero', [
    'kicker' => 'Mentions légales',
    'title' => 'Informations<br><em>légales.</em>',
    'intro' => 'Les informations d’édition du site sont en cours de complétion avec l’établissement.',
    'image' => 'assets/images/gallery-detail.jpg',
    'imageAlt' => 'Illustration d’un détail intérieur — visuel non contractuel',
    'aside' => 'À compléter<br>avant mise en production',
]);
?>
<section class="section prose-page">
    <div class="shell">
        <div class="prose">
            <div class="notice">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                <p><strong>Document provisoire.</strong> Les mentions légales doivent être complétées et validées avant la mise en ligne publique du site.</p>
            </div>

            <h2>Éditeur du site</h2>
            <p>Brazza Hotel — Brazzaville, République du Congo.<br>
            Dénomination légale : [à compléter]<br>
            Adresse : [à compléter]<br>
            Téléphone : [à compléter]<br>
            E-mail : [à compléter]<br>
            Numéro d’immatriculation / RCCM : [à compléter]</p>

            <h2>Directeur de la publication</h2>
            <p>[Nom et fonction à compléter]</p>

            <h2>Hébergement</h2>
            <p>Hébergeur : [nom, adresse et téléphone à compléter].</p>

            <h2>Propriété intellectuelle</h2>
            <p>La marque, les textes éditoriaux validés et les photographies officielles de Brazza Hotel sont protégés. Les visuels actuellement affichés proviennent de banques d’images (Unsplash) et sont clairement identifiés comme illustrations non contractuelles ; ils ne représentent pas l’établissement.</p>

            <h2>Responsabilité</h2>
            <p>Les informations publiées sont fournies à titre indicatif. Les tarifs, disponibilités, prestations et horaires officiels ne sont contractuels qu’après confirmation par l’hôtel. Les demandes envoyées via le site ne valent pas réservation confirmée.</p>

            <h2>Liens externes</h2>
            <p>Le site peut intégrer une carte Google Maps et des liens vers des services tiers. Brazza Hotel n’exerce aucun contrôle sur ces services et décline toute responsabilité quant à leurs contenus ou politiques de confidentialité.</p>

            <h2>Signaler un problème</h2>
            <p>Pour signaler une erreur, un contenu inapproprié ou un problème technique, utilisez la page <a href="<?= e(route('contact.index')) ?>">Contact</a>.</p>

            <p style="margin-top: 2.5rem;"><a href="<?= e(route('legal.privacy')) ?>">Politique de confidentialité</a></p>
        </div>
    </div>
</section>
