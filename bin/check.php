<?php

declare(strict_types=1);

/**
 * Environment and installation diagnostic.
 * Usage: php bin/check.php
 * Never exposes database credentials.
 */

define('BASE_PATH', dirname(__DIR__));

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require BASE_PATH . '/app/bootstrap.php';

$ok = true;
$line = static function (bool $condition, string $label, string $detail = '') use (&$ok): void {
    if (!$condition) {
        $ok = false;
    }
    echo ($condition ? '[OK]   ' : '[FAIL] ') . $label . ($detail !== '' ? ' — ' . $detail : '') . PHP_EOL;
};

echo "Brazza Hotel — diagnostic d'installation" . PHP_EOL;
echo str_repeat('-', 52) . PHP_EOL;

$line(version_compare(PHP_VERSION, '8.2.0', '>='), 'PHP 8.2+', PHP_VERSION);

foreach (['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'openssl'] as $extension) {
    $line(extension_loaded($extension), 'Extension ' . $extension);
}

$line(is_file(BASE_PATH . '/.env'), 'Fichier .env', is_file(BASE_PATH . '/.env') ? 'présent' : 'copier .env.example → .env');
$line(config('app.debug') === false || config('app.env') !== 'production', 'Mode debug cohérent', config('app.debug') ? 'debug actif (local)' : 'debug désactivé');
$line((string) config('app.url') !== '', 'APP_URL défini', (string) config('app.url'));
$canWrite = static function (string $directory): bool {
    if (!is_dir($directory)) {
        return false;
    }
    $probe = $directory . '/.write-test-' . bin2hex(random_bytes(4));
    $written = @file_put_contents($probe, 'ok');
    if ($written === false) {
        return false;
    }
    @unlink($probe);
    return true;
};
$line($canWrite(BASE_PATH . '/storage/logs'), 'Dossier logs accessible en écriture');
$line($canWrite(BASE_PATH . '/public/assets/uploads'), 'Dossier uploads accessible en écriture');

try {
    $pdo = db();
    $line(true, 'Connexion MySQL', (string) config('database.database'));
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $required = ['admins', 'rooms', 'room_images', 'reservations', 'gallery_categories', 'gallery_images', 'hotel_settings', 'hotel_services', 'contact_messages'];
    $missing = array_diff($required, $tables);
    $line($missing === [], 'Tables principales', $missing === [] ? count($tables) . ' tables' : 'manquantes : ' . implode(', ', $missing));
    $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($adminCount > 0) {
        echo '[OK]   Compte administrateur — ' . $adminCount . ' compte(s)' . PHP_EOL;
    } else {
        echo '[WARN] Aucun compte administrateur — créez-en un avec bin/create-admin.php' . PHP_EOL;
    }
    $demoRooms = (int) $pdo->query("SELECT COUNT(*) FROM rooms WHERE content_status = 'demo'")->fetchColumn();
    echo '[INFO] ' . $demoRooms . " catégorie(s) de démonstration encore publiée(s)." . PHP_EOL;
} catch (Throwable $exception) {
    $line(false, 'Connexion MySQL', 'vérifier .env, MySQL et la base brazza_hotel');
}

echo str_repeat('-', 52) . PHP_EOL;
echo $ok ? "Installation prête.\n" : "Corrigez les points signalés avant de continuer.\n";
exit($ok ? 0 : 1);
