<?php
partial('components/page-hero', [
    'kicker' => 'Confidentialité',
    'title' => 'Vos données,<br><em>notre attention.</em>',
    'intro' => 'Cette page explique quelles données sont collectées par les formulaires du site et comment elles sont utilisées.',
    'image' => 'assets/images/room-classic.jpg',
    'imageAlt' => 'Illustration d’une chambre d’hôtel — visuel non contractuel',
    'aside' => 'Document à valider<br>par l’établissement',
]);
?>
<section class="section prose-page">
    <div class="shell">
        <div class="prose">
            <div class="notice">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                <p><strong>Version provisoire.</strong> Cette politique doit être relue et validée par l’hôtel et, si nécessaire, par un conseil juridique compétent en République du Congo. Les champs entre crochets restent à compléter.</p>
            </div>

            <h2>1. Responsable du traitement</h2>
            <p>Le site est édité pour Brazza Hotel, Brazzaville, République du Congo. Les coordonnées complètes du responsable de traitement sont à confirmer : [dénomination légale, adresse, e-mail, téléphone].</p>

            <h2>2. Données collectées</h2>
            <p>Les formulaires de contact et de demande de réservation collectent uniquement les données que vous choisissez de transmettre :</p>
            <ul>
                <li>nom complet ;</li>
                <li>numéro de téléphone ;</li>
                <li>adresse e-mail, facultative pour la réservation ;</li>
                <li>dates de séjour, nombre de voyageurs et message ;</li>
                <li>sujet et contenu du message de contact.</li>
            </ul>
            <p>Une empreinte technique non réversible de l’adresse IP peut être enregistrée à des fins de sécurité et de limitation des abus. L’adresse IP brute n’est pas conservée par les formulaires publics.</p>

            <h2>3. Finalités</h2>
            <p>Les données sont utilisées pour répondre à vos messages, traiter votre demande de réservation, organiser votre séjour et assurer la sécurité du site. Elles ne sont ni vendues ni cédées à des tiers à des fins commerciales.</p>

            <h2>4. Base légale et consentement</h2>
            <p>Le traitement repose sur votre consentement, recueilli via la case à cocher présente sur les formulaires, et sur l’intérêt légitime de l’hôtel à répondre aux demandes qu’il reçoit.</p>

            <h2>5. Durée de conservation</h2>
            <p>Les demandes de réservation sont conservées le temps nécessaire au traitement de la demande et à la gestion de la relation client. Les messages de contact sont conservés pour une durée limitée, à définir avec l’hôtel. [Durées précises à compléter.]</p>

            <h2>6. Cookies et sessions</h2>
            <p>Le site utilise un cookie de session strictement nécessaire au fonctionnement des formulaires et de l’espace d’administration. Aucun cookie publicitaire ni traceur tiers n’est installé par le site. La carte Google Maps est un contenu intégré susceptible de déposer ses propres cookies lorsque vous l’affichez.</p>

            <h2>7. Vos droits</h2>
            <p>Vous pouvez demander l’accès, la rectification ou la suppression de vos données, ainsi que la limitation de leur traitement. Pour exercer ces droits, contactez l’hôtel aux coordonnées qui seront publiées sur la page <a href="<?= e(route('contact.index')) ?>">Contact</a>. [Procédure et autorité de contrôle à compléter.]</p>

            <h2>8. Sécurité</h2>
            <p>Les échanges de formulaires sont protégés par un jeton anti-CSRF, les données sont validées côté serveur, les requêtes SQL utilisent des requêtes préparées et les mots de passe des administrateurs sont hachés. Aucun système n’étant infaillible, l’hôtel s’engage à corriger sans délai toute vulnérabilité portée à sa connaissance.</p>

            <p style="margin-top: 2.5rem;"><a href="<?= e(route('legal.mentions')) ?>">Consulter les mentions légales</a></p>
        </div>
    </div>
</section>
