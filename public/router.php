<?php

declare(strict_types=1);

/**
 * Router for PHP's built-in development server:
 *   php -S 127.0.0.1:8000 -t public public/router.php
 *
 * Existing static assets are served directly; every other request is handled
 * by the front controller.
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = __DIR__ . (is_string($path) ? $path : '/');

if (is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
