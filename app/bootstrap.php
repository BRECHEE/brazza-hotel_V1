<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Env;
use App\Core\Security;
use App\Core\Session;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

/**
 * PSR-4 autoloader for the App namespace. Composer is optional: if a vendor
 * autoloader is present it is used, otherwise this tiny loader takes over.
 */
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/Helpers/functions.php';

Env::load(BASE_PATH . '/.env');

$appConfig = require BASE_PATH . '/config/app.php';
$databaseConfig = require BASE_PATH . '/config/database.php';

date_default_timezone_set((string) $appConfig['timezone']);
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'French_France');

error_reporting(E_ALL);
ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');

$app = new Application(BASE_PATH, [
    'app' => $appConfig,
    'database' => $databaseConfig,
]);

Security::sendHeaders();
Session::start($appConfig['session']);

require BASE_PATH . '/routes/web.php';

return $app;
