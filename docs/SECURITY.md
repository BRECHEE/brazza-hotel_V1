# Sécurité — Brazza Hotel

## Authentification

- Mots de passe hachés avec `password_hash(PASSWORD_DEFAULT)`.
- Vérification `password_verify()` avec rehash transparent.
- Aucun compte administrateur livré par défaut : création via CLI.
- Limitation à 5 tentatives par session / 10 minutes, message générique, délai aléatoire.
- Régénération de l'identifiant de session et rotation du jeton CSRF à la connexion et à la déconnexion.
- Expiration d'inactivité (par défaut 2 h) + vérification du compte actif à chaque requête.
- En-têtes `Cache-Control: no-store` et `X-Robots-Tag: noindex` sur l'administration.

## Sessions

- Cookie `HttpOnly`, `SameSite=Lax`, `Secure` activable en production.
- `session.use_strict_mode=1`, `session.use_only_cookies=1`.
- Identifiants de session longs, destruction complète à la déconnexion.
- Les messages flash sont limités à une requête et ne contiennent jamais de mot de passe.

## CSRF

- Jeton aléatoire de 32 octets stocké en session.
- `csrf_field()` sur chaque formulaire POST.
- `verify_csrf()` avec `hash_equals()` avant toute action d'écriture.
- Couvre aussi la connexion et la déconnexion.
- Une session expirée renvoie une page 419 neutre.

## Entrées et sorties

- Validation serveur centralisée (`Validator`) et règles métier (dates, capacité, tarifs).
- Longueurs maximales sur tous les champs.
- Honeypot sur les formulaires publics.
- Limitation de débit par session sur contact, réservation, connexion.
- Échappement systématique à l'affichage (`e()`), aucun HTML utilisateur brut.
- Aucune injection SQL : PDO préparé, émulation désactivée.

## Téléversements

- `is_uploaded_file` + code d'erreur PHP.
- Taille maximale (4 Mo par défaut).
- Type MIME réel via `finfo`, liste blanche JPEG/PNG/WebP.
- Vérification `getimagesize` et dimensions minimales/maximales.
- Nom aléatoire, extension imposée par le MIME — jamais le nom client.
- Stockage dans `public/assets/uploads/` avec `.htaccess` interdisant PHP/CGI/SVG/HTML.
- Suppression contrôlée : chemin résolu, confinement au dossier uploads, pas de `..`.
- Les fichiers de démonstration du dossier `assets/images` ne sont jamais supprimés par l'admin.

## Configuration et journalisation

- `.env` ignoré par Git, jamais dans `public/`.
- Erreurs techniques journalisées dans `storage/logs/` hors document root.
- Mode debug désactivé en production ; page 500 neutre.
- Requêtes préparées, aucun secret en dur dans le code.
- Les adresses IP des formulaires publics sont hachées (non réversibles) ; l'IP brute n'est pas stockée.
- Le journal d'activité admin ne stocke ni mot de passe ni contenu sensible.

## En-têtes HTTP

- Content-Security-Policy : `default-src 'self'`, scripts nonce + self, iframes limitées à Google Maps.
- `X-Content-Type-Options: nosniff`.
- `X-Frame-Options: SAMEORIGIN` + `frame-ancestors 'self'`.
- `Referrer-Policy: strict-origin-when-cross-origin`.
- `Permissions-Policy` désactivant caméra, micro, géolocalisation.
- HSTS si HTTPS.
- Apache : `Options -Indexes`, blocage des extensions sensibles, cache et compression.

## Risques résiduels et recommandations de production

1. **Limitation de débit par session** : ajouter un rate limit IP au niveau Apache/Cloudflare/WAF.
2. **Notifications e-mail** non intégrées : les messages sont consultés dans l'administration.
3. **Sauvegardes** : mettre en place dump MySQL chiffré + sauvegarde des uploads.
4. **MFA** : recommandé pour les comptes `owner` si l'hébergement le permet.
5. **Revue juridique** : mentions légales et politique de confidentialité à valider pour le Congo.
6. **Images** : remplacer les illustrations et conserver les autorisations écrites des photographes.
7. **HTTPS obligatoire** en production, redirection HTTP → HTTPS et `SESSION_SECURE=true`.
8. **Compte MySQL dédié** avec droits limités à la base `brazza_hotel`.

## Procédure en cas d'incident

1. Désactiver le compte concerné (`admins.is_active = 0`).
2. Réinitialiser le mot de passe via `bin/create-admin.php` après suppression/nouveau compte.
3. Vérifier `storage/logs/` et `admin_activity_log`.
4. Sauvegarder les preuves, puis restaurer depuis une sauvegarde saine si nécessaire.
5. Forcer la rotation de `APP_URL`, secrets SMTP et accès MySQL.
