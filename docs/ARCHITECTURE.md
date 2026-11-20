# Architecture — Brazza Hotel

## 1. Principes

L'application est volontairement sans framework : le périmètre est maîtrisé, l'hébergement mutualisé
PHP/MySQL reste possible et il n'y a aucune étape de build obligatoire. Le code suit une séparation
inspirée de MVC :

- **Core** : infrastructure HTTP, routage, sessions, vues, base de données, sécurité.
- **Controllers** : orchestration des requêtes et des réponses ; aucun HTML.
- **Models** : accès aux données via PDO et requêtes préparées.
- **Services** : logique transverse (authentification, upload, limitation de débit, journal).
- **Views** : templates PHP échappés, sans requête SQL.
- **Config / routes / public** : configuration, déclaration des routes, unique point d'entrée web.

## 2. Cycle d'une requête

```text
Navigateur
  → public/index.php
    → app/bootstrap.php
      → autoload PSR-4 + helpers + .env + config
      → Security::sendHeaders() (CSP, X-Frame-Options…)
      → Session::start() (cookie HttpOnly, SameSite, flash, CSRF)
      → routes/web.php + routes/admin.php
    → Request::capture()
    → Router::dispatch() → [Controller, méthode]
      → validation / CSRF
      → Model (PDO préparé)
      → View::render() (layout + vue)
    → réponse HTML / redirection / XML / texte
```

Toute exception non gérée est journalisée et une page 500 neutre est renvoyée sans détail sensible.

## 3. Routage

`App\Core\Router` gère les routes nommées et les segments `{paramètre}`. Les contrôleurs sont instanciés
à la demande. Les routes d'administration sont déclarées séparément et protégées par
`AdminController::__construct()`.

URLs publiques en français, sans extension : `/chambres`, `/galerie`, `/reservation`.

## 4. Vues et composants

`App\Core\View` isole les variables, capture le contenu de la vue puis l'injecte dans un layout.
Composants réutilisables :

- `layouts/app.php` — site public ;
- `layouts/admin.php` — back-office ;
- `layouts/admin-auth.php` — connexion ;
- `layouts/minimal.php` — erreurs ;
- `components/picture.php` — `<picture>` WebP + fallback JPEG + lazy loading ;
- `components/page-hero.php`, `header.php`, `footer.php`, `alerts.php`, `cta-band.php`, `lightbox.php`.

Les données sont échappées avec `e()` (`htmlspecialchars`, `ENT_QUOTES | ENT_SUBSTITUTE`, UTF-8).

## 5. Base de données

Schéma normalisé, InnoDB, utf8mb4. Les relations :

```text
rooms 1 ─── N room_images
rooms N ─── N amenities  (room_amenities)
rooms 1 ─── N reservations (ON DELETE SET NULL pour conserver l'historique)
gallery_categories 1 ─── N gallery_images (SET NULL)
admins 1 ─── N admin_activity_log (SET NULL)
```

Les tarifs sont `NULL` lorsqu'ils ne sont pas officiels. `content_status` (`demo` / `verified`)
matérialise la séparation entre contenu éditorial de démonstration et information validée.

## 6. Séparation des responsabilités

- Aucune requête SQL dans les vues.
- Aucun HTML dans les contrôleurs : `render()` charge une vue.
- Aucun `$_POST` brut sans validation ; `Request` centralise les entrées.
- Les téléversements passent tous par `UploadService`.
- Les actions d'administration sensibles sont journalisées dans `admin_activity_log`.
- Les paramètres du site passent par `Setting`, avec cache par requête et drapeau de vérification.

## 7. Thèmes clair / sombre

Le thème est piloté par l'attribut `data-theme` sur `<html>` :

- `dark` par défaut ;
- `light` via le bouton d'en-tête (`data-theme-toggle`) ;
- choix mémorisé dans `localStorage` sous `brazza-theme` ;
- un script inline à nonce CSP applique le thème avant le premier rendu pour éviter le flash ;
- le JavaScript met à jour `aria-pressed`, le libellé accessible et la balise `theme-color` ;
- le thème clair conserve les zones photographiques sombres (hero, lightbox, carte) pour préserver
  la direction artistique et la lisibilité.

## 8. Choix techniques notables

- **Sans Composer obligatoire** : autoloader PSR-4 local ; `composer.json` fourni si Composer est disponible.
- **CSS/JS/fonts auto-hébergés** : pas de dépendance CDN, meilleure disponibilité et confidentialité.
- **GSAP uniquement sur l'accueil**, désactivé si `prefers-reduced-motion`.
- **Images responsives WebP** générées en amont, fallback JPEG pour Open Graph et anciens navigateurs.
- **Sessions PHP natives** durcies plutôt qu'un JWT : révocation immédiate et cookie HttpOnly.
- **Demandes de réservation** : pas de moteur de disponibilité, pas de paiement, pas de promesse automatique.

## 9. Évolutions prévues

- Notifications e-mail (SMTP) pour les demandes et messages.
- Gestion complète des services et de la carte du restaurant.
- Moteur de disponibilité relié à un PMS, uniquement si l'hôtel le fournit.
- Sauvegardes automatisées et supervision.
- Version anglaise, si confirmée par l'hôtel.
