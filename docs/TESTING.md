# Plan de tests — Brazza Hotel

## Préparation

```powershell
php -l public\index.php
php bin\check.php
php -S localhost:8000 -t public public/router.php
```

Créer un administrateur de test si nécessaire :

```powershell
$env:ADMIN_PASSWORD = "Test-Uniquement-2026!"
php bin\create-admin.php --name="Test QA" --email="qa@brazza.test" --role=manager
```

## 1. Site public

| # | Action | Attendu |
|---|---|---|
| 1 | Ouvrir `/` sur desktop 1440 px | Hero, navigation, formulaire de séjour, sections complètes |
| 2 | Ouvrir `/` sur mobile 390 px | Menu burger, boutons pleine largeur, aucune fuite horizontale |
| 3 | `/chambres` | 3 catégories de démonstration, tarifs « sur demande », badges « Aperçu » |
| 4 | `/chambres/chambre-classique` | Fiche, équipements, CTA réservation, lightbox si images |
| 5 | `/galerie` et filtres | Filtrage serveur, lightbox clavier, compteur correct |
| 6 | `/restaurant`, `/a-propos` | Aucune donnée officielle inventée, mentions de démonstration visibles |
| 7 | `/sitemap.xml`, `/robots.txt` | XML/texte valides, URL absolues, `/admin` exclu |
| 8 | Bouton thème dans l'en-tête | Bascule sombre ↔ clair, icône et libellé ARIA mis à jour |
| 9 | Rechargement après choix clair | Le thème clair est appliqué avant le premier rendu, sans flash |
| 10 | Thème clair sur mobile | Lisibilité, contraste des boutons or et formulaires, menu mobile |

## 2. Formulaires publics

| # | Action | Attendu |
|---|---|---|
| 1 | Contact valide | 303, message en base, flash de succès |
| 2 | Contact sans consentement | Retour formulaire, erreur serveur, aucune insertion |
| 3 | Contact sans jeton CSRF | HTTP 419 |
| 4 | Réservation valide | 303 vers `/reservation/merci`, référence `BRZ-…` en base |
| 5 | Départ ≤ arrivée | Erreur serveur, aucune insertion |
| 6 | Date passée | Erreur serveur, aucune insertion |
| 7 | Plus de 5 demandes en 20 min | Message de limitation de débit |
| 8 | Honeypot rempli | Redirection silencieuse, aucune insertion |

## 3. Administration

| # | Action | Attendu |
|---|---|---|
| 1 | `/admin` sans session | Redirection vers `/admin/connexion` |
| 2 | Mauvais mot de passe ×5 | Blocage temporaire, message générique |
| 3 | Connexion valide | 303 vers tableau de bord, session régénérée |
| 4 | Déconnexion POST | Session détruite, jeton CSRF tourné |
| 5 | CRUD chambre | Création, modification, suppression, slug unique |
| 6 | Upload image | Fichier renommé, MIME validé, visible si actif |
| 7 | Upload fichier `.php` | Refus serveur, aucun fichier exécutable |
| 8 | Suppression image | Fichier uploadé supprimé, référence DB supprimée |
| 9 | Réservation → statut | Mise à jour DB + journal d'activité |
| 10 | Message → statut | Mise à jour DB + journal |
| 11 | Paramètres | Valeur enregistrée, drapeau vérifié, cache vidé |
| 12 | Changement de mot de passe | Ancien mot de passe requis, session régénérée |

## 4. Sécurité et en-têtes

```powershell
curl.exe -I http://localhost:8000/
curl.exe -I http://localhost:8000/admin/connexion
```

Vérifier la présence de `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`,
`Referrer-Policy`, `Permissions-Policy`.

Vérifier que les URL suivantes ne sont pas accessibles : `/.env`, `/config/app.php`, `/database/brazza_hotel.sql`,
`/storage/logs/php-error.log`, `/app/bootstrap.php`.

## 5. Base de données

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "USE brazza_hotel; SHOW TABLES; SELECT COUNT(*) FROM rooms;"
```

Vérifier :
- aucune colonne mot de passe en clair ;
- `SELECT password_hash FROM admins` commence par `$2y$` ou `$argon2` ;
- `price_per_night` est `NULL` pour les tarifs non fournis ;
- les contraintes de dates et de clés étrangères sont actives.

## 6. Performance et accessibilité

- Lighthouse mobile/desktop (performance, accessibilité, SEO).
- Navigation clavier complète, focus visible.
- `prefers-reduced-motion` : animations neutralisées.
- Images WebP servies avec `srcset`, dimensions déclarées, lazy loading hors hero.
- Aucun défilement horizontal à 320, 390, 768, 1024 et 1440 px.

## Nettoyage après tests

Supprimer les demandes/messages de test depuis l'administration ou la base, ainsi que le compte QA :

```sql
USE brazza_hotel;
DELETE FROM reservations WHERE guest_name LIKE 'Test%';
DELETE FROM contact_messages WHERE email LIKE '%@brazza.test';
DELETE FROM admins WHERE email = 'qa@brazza.test';
```
