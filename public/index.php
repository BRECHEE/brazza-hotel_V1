<?php

declare(strict_types=1);

/**
 * Brazza Hotel — single public entry point.
 *
 * The web server document root MUST point to this directory, never to the
 * project root. Configuration, application code and storage stay outside it.
 */

define('BASE_PATH', dirname(__DIR__));

try {
    require BASE_PATH . '/app/bootstrap.php';
    app()->router()->dispatch(App\Core\Request::capture());
} catch (Throwable $exception) {
    error_log('[unhandled] ' . $exception::class . ': ' . $exception->getMessage()
        . ' in ' . $exception->getFile() . ':' . $exception->getLine());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    try {
        echo view('errors/500', [
            'meta' => ['title' => 'Service momentanément indisponible', 'robots' => 'noindex, nofollow'],
            'debugMessage' => (bool) config('app.debug') ? $exception->getMessage() : null,
        ], 'layouts/minimal');
    } catch (Throwable) {
        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><title>Erreur</title>'
            . '<body style="background:#101010;color:#f4efe6;font-family:sans-serif;padding:3rem">'
            . '<h1>Service momentanément indisponible</h1><p>Merci de réessayer dans quelques instants.</p>'
            . '</body></html>';
    }
}
