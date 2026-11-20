<?php

declare(strict_types=1);

/**
 * Application configuration.
 *
 * Values are read from the environment (see .env.example). Safe defaults are
 * provided so a fresh local checkout can boot, but production secrets must be
 * supplied through the web-server environment or an untracked .env file.
 */
return [
    'name'     => env('APP_NAME', 'Brazza Hotel'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url'      => rtrim((string) env('APP_URL', 'http://localhost:8000'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Brazzaville'),
    'locale'   => env('APP_LOCALE', 'fr'),
    'currency' => 'XAF',

    'session' => [
        'name'     => env('SESSION_NAME', 'brazza_hotel_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'secure'   => filter_var(env('SESSION_SECURE', false), FILTER_VALIDATE_BOOL),
        'samesite' => 'Lax',
    ],

    'uploads' => [
        'max_bytes' => (int) env('UPLOAD_MAX_BYTES', 4_194_304),
        'max_width' => 6000,
        'max_height' => 6000,
        'rooms_dir' => BASE_PATH . '/public/assets/uploads/rooms',
        'gallery_dir' => BASE_PATH . '/public/assets/uploads/gallery',
    ],

    /*
     * Only the hotel name, its city and its country are verified at this stage.
     * Everything else is intentionally blank and rendered as "À confirmer".
     */
    'hotel' => [
        'name'       => 'Brazza Hotel',
        'city'       => 'Brazzaville',
        'country'    => 'République du Congo',
        'phone'      => (string) env('HOTEL_PHONE', ''),
        'whatsapp'   => (string) env('HOTEL_WHATSAPP', ''),
        'email'      => (string) env('HOTEL_EMAIL', ''),
        'address'    => (string) env('HOTEL_ADDRESS', ''),
        'maps_query' => (string) env('HOTEL_MAPS_QUERY', 'Brazzaville, République du Congo'),
    ],

    'mail' => [
        'to' => (string) env('MAIL_TO', ''),
    ],
];
