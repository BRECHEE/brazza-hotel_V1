<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Immutable snapshot of the current HTTP request.
 */
final class Request
{
    /** @param array<string, mixed> $query @param array<string, mixed> $body @param array<string, mixed> $files */
    private function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $body,
        public readonly array $files,
        public readonly string $ip,
        public readonly string $userAgent
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $rawPath = parse_url($uri, PHP_URL_PATH);
        $path = is_string($rawPath) ? rawurldecode($rawPath) : '/';
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        return new self(
            $method === 'HEAD' ? 'GET' : $method,
            $path,
            $_GET,
            $_POST,
            $_FILES,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
            substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255)
        );
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function string(string $key, string $default = ''): string
    {
        $value = $this->input($key, $default);
        return is_scalar($value) ? trim((string) $value) : $default;
    }

    public function integer(string $key, int $default = 0): int
    {
        $value = filter_var($this->input($key), FILTER_VALIDATE_INT);
        return $value === false ? $default : (int) $value;
    }

    public function boolean(string $key): bool
    {
        return filter_var($this->input($key, false), FILTER_VALIDATE_BOOL);
    }

    /** @return array<string, mixed> */
    public function file(string $key): array
    {
        $file = $this->files[$key] ?? [];
        return is_array($file) ? $file : [];
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function wantsJson(): bool
    {
        return str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json');
    }
}
