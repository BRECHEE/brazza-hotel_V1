<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Database;
use App\Core\Request;
use App\Core\Security;
use App\Core\Session;
use App\Models\Setting;

/**
 * Global template and application helpers.
 */

function app(): Application
{
    return Application::instance();
}

function config(string $key, mixed $default = null): mixed
{
    return app()->config($key, $default);
}

function env(string $key, mixed $default = null): mixed
{
    return \App\Core\Env::get($key, $default);
}

function view(string $view, array $data = [], ?string $layout = 'layouts/app'): string
{
    return app()->view()->render($view, $data, $layout);
}

function partial(string $view, array $data = []): void
{
    app()->view()->partial($view, $data);
}

/**
 * Build a WebP srcset for a local image when derivatives exist.
 *
 * @return array{src: string, srcset: string}
 */
function image_sources(string $path): array
{
    $src = media_url($path);
    $srcset = [];

    if (!preg_match('#^https?://#i', $path) && str_starts_with(ltrim($path, '/'), 'assets/images/')) {
        $base = preg_replace('/\.[a-z0-9]+$/i', '', ltrim($path, '/'));
        $optimizedBase = preg_replace('#^assets/images/#', 'assets/images/optimized/', (string) $base);

        foreach ([480, 960, 1600] as $width) {
            $optimized = BASE_PATH . '/public/' . $optimizedBase . '-' . $width . '.webp';
            if (is_file($optimized)) {
                $srcset[] = url($optimizedBase . '-' . $width . '.webp') . ' ' . $width . 'w';
            }
        }
    }

    return ['src' => $src, 'srcset' => implode(', ', $srcset)];
}

function db(): PDO
{
    return Database::connection();
}

function request(): Request
{
    static $request = null;
    return $request ??= Request::capture();
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $base = (string) config('app.url', '');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = BASE_PATH . '/public/' . $path;
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url($path) . '?v=' . $version;
}

function media_url(?string $path, ?string $fallback = null): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return asset($fallback ?? 'assets/images/lobby.jpg');
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return url(ltrim($path, '/'));
}

function route(string $name, array $parameters = []): string
{
    return url(app()->router()->urlFor($name, $parameters));
}

function csrf_token(): string
{
    return Session::csrfToken();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (!Session::verifyCsrf(is_string($_POST['_token'] ?? null) ? $_POST['_token'] : null)) {
        http_response_code(419);
        echo view('errors/419', ['meta' => ['title' => 'Session expirée']], 'layouts/minimal');
        exit;
    }
}

function flash(string $key, mixed $value): void
{
    Session::flash($key, $value);
}

function flashed(string $key, mixed $default = null): mixed
{
    return Session::flashed($key, $default);
}

function old(string $key, string $default = ''): string
{
    return e(Session::old($key, $default));
}

function has_old(): bool
{
    $old = Session::flashed('_old', null);
    return is_array($old);
}

function old_checked(string $key, bool $fallback = false): bool
{
    if (!has_old()) {
        return $fallback;
    }
    $old = Session::flashed('_old', []);
    return is_array($old) && !empty($old[$key]);
}

function slugify(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    if (function_exists('iconv')) {
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($transliterated)) {
            $value = $transliterated;
        }
    }
    $value = mb_strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function admin_is(string $path, bool $exact = false): bool
{
    return is_current($path, $exact);
}

function error_for(string $field): string
{
    $errors = Session::errors();
    return isset($errors[$field]) ? e((string) $errors[$field]) : '';
}

function has_error(string $field): bool
{
    return isset(Session::errors()[$field]);
}

function setting(string $key, string $default = ''): string
{
    return Setting::get($key, $default);
}

function setting_verified(string $key): bool
{
    return Setting::isVerified($key);
}

function filled(?string $value): bool
{
    return $value !== null && trim($value) !== '';
}

function contact_value(?string $value, string $placeholder = 'À confirmer'): string
{
    return filled($value) ? (string) $value : $placeholder;
}

function phone_href(string $phone): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

function whatsapp_href(string $message = ''): ?string
{
    $raw = setting('hotel_whatsapp', (string) config('app.hotel.whatsapp'));
    $number = preg_replace('/\D+/', '', $raw);
    if ($number === null || strlen($number) < 6) {
        return null;
    }

    $url = 'https://wa.me/' . $number;
    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }

    return $url;
}

function is_current(string $path, bool $exact = false): bool
{
    $current = request()->path;
    $path = '/' . trim($path, '/');
    return $exact ? $current === $path : ($current === $path || str_starts_with($current, $path . '/'));
}

function nav_class(string $path, bool $exact = false): string
{
    return is_current($path, $exact) ? 'is-active' : '';
}

function body_class(string $class = ''): string
{
    return trim('site-body ' . $class);
}

function status_label(string $status): string
{
    return [
        'new' => 'Nouvelle',
        'contacted' => 'Contactée',
        'confirmed' => 'Confirmée',
        'declined' => 'Refusée',
        'cancelled' => 'Annulée',
        'read' => 'Lue',
        'replied' => 'Répondue',
        'archived' => 'Archivée',
    ][$status] ?? ucfirst($status);
}

function reservation_status_class(string $status): string
{
    return 'status status--' . preg_replace('/[^a-z_]/', '', $status);
}

function format_date_fr(?string $date, bool $withYear = true): string
{
    if (!$date) {
        return '—';
    }

    try {
        $value = new DateTimeImmutable($date);
    } catch (Exception) {
        return '—';
    }

    $months = [
        1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
    ];
    $label = $value->format('j') . ' ' . $months[(int) $value->format('n')];
    return $withYear ? $label . ' ' . $value->format('Y') : $label;
}

function format_datetime_fr(?string $date): string
{
    return $date ? format_date_fr(substr($date, 0, 10)) . ' · ' . substr($date, 11, 5) : '—';
}

function price_display(?string $amount, string $currency = 'XAF'): string
{
    if ($amount === null || (float) $amount <= 0) {
        return 'Tarif sur demande';
    }

    $formatted = number_format((float) $amount, 0, ',', ' ');
    return $formatted . ' ' . ($currency === 'XAF' ? 'FCFA' : $currency) . ' / nuit';
}

function excerpt(?string $text, int $length = 140): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length - 1) . '…';
}

function csp_nonce(): string
{
    return Security::nonce();
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    return $initials !== '' ? $initials : 'BH';
}

function star_icon(bool $filled = true): string
{
    return '<i class="fa-' . ($filled ? 'solid' : 'regular') . ' fa-star" aria-hidden="true"></i>';
}

function map_embed_url(): string
{
    $query = setting('hotel_maps_query', (string) config('app.hotel.maps_query', 'Brazzaville, République du Congo'));
    return 'https://www.google.com/maps?q=' . rawurlencode($query) . '&output=embed&hl=fr&z=12';
}

function map_link_url(): string
{
    $query = setting('hotel_maps_query', (string) config('app.hotel.maps_query', 'Brazzaville, République du Congo'));
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
}

function current_year(): string
{
    return (new DateTimeImmutable('now'))->format('Y');
}
