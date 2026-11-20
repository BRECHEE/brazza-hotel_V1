<?php

declare(strict_types=1);

/**
 * PDO connection settings. The credential set is read from environment
 * variables; the defaults target a stock XAMPP installation in development.
 */
return [
    'host'     => env('DB_HOST', '127.0.0.1'),
    'port'     => (int) env('DB_PORT', 3306),
    'database' => env('DB_NAME', 'brazza_hotel'),
    'username' => env('DB_USER', 'root'),
    'password' => (string) env('DB_PASSWORD', ''),
    'charset'  => env('DB_CHARSET', 'utf8mb4'),
];
