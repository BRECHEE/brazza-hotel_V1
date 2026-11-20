<?php

declare(strict_types=1);

/**
 * Create the first administrator account.
 *
 * Usage (PowerShell):
 *   $env:ADMIN_PASSWORD = "A long unique passphrase"
 *   php bin/create-admin.php --name="Nom" --email="admin@example.com"
 *
 * The password is never written to the database in cleartext.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/bootstrap.php';

use App\Models\Admin;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$options = getopt('', ['name:', 'email:', 'role:', 'password:']);
$name = trim((string) ($options['name'] ?? ''));
$email = mb_strtolower(trim((string) ($options['email'] ?? '')));
$role = (string) ($options['role'] ?? 'owner');

if ($name === '') {
    $name = trim((string) readline('Nom complet : '));
}
if ($email === '') {
    $email = mb_strtolower(trim((string) readline('Adresse e-mail : ')));
}

$password = (string) ($options['password'] ?? getenv('ADMIN_PASSWORD') ?: '');
if ($password === '') {
    fwrite(STDOUT, "Mot de passe (saisie visible, préférez ADMIN_PASSWORD) : ");
    $password = trim((string) fgets(STDIN));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Adresse e-mail invalide.\n");
    exit(1);
}
if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
    fwrite(STDERR, "Le nom doit contenir entre 2 et 120 caractères.\n");
    exit(1);
}
if (!in_array($role, ['owner', 'manager', 'editor'], true)) {
    fwrite(STDERR, "Rôle invalide. Valeurs acceptées : owner, manager, editor.\n");
    exit(1);
}
if (strlen($password) < 12) {
    fwrite(STDERR, "Le mot de passe doit contenir au moins 12 caractères.\n");
    exit(1);
}
if (Admin::findByEmail($email) !== null) {
    fwrite(STDERR, "Un compte actif utilise déjà cette adresse e-mail.\n");
    exit(1);
}

$id = Admin::create($name, $email, $password, $role);

fwrite(STDOUT, "Administrateur créé (#" . $id . ") : " . $email . "\n");
fwrite(STDOUT, "Conservez la phrase de passe dans un gestionnaire de mots de passe et supprimez-la de votre historique.\n");
