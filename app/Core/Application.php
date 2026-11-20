<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Small application container. It exposes configuration, routing, view
 * rendering and the PDO connection without introducing a framework.
 */
final class Application
{
    private static ?self $instance = null;
    private Router $router;
    private View $view;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly string $basePath,
        private readonly array $config
    ) {
        self::$instance = $this;
        $this->router = new Router();
        $this->view = new View($basePath . '/resources/views');
    }

    public static function instance(): self
    {
        if (!self::$instance instanceof self) {
            throw new RuntimeException('The application has not been booted.');
        }

        return self::$instance;
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $value = $this->config;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function view(): View
    {
        return $this->view;
    }
}
