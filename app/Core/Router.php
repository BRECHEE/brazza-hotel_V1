<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

/**
 * Named route collection with {parameter} matching.
 *
 * A route action is either a Closure or [Controller::class, 'method'].
 */
final class Router
{
    /** @var array<int, array{method: string, uri: string, pattern: string, action: mixed, name: ?string}> */
    private array $routes = [];

    /** @var array<string, string> */
    private array $names = [];

    public function get(string $uri, mixed $action, ?string $name = null): void
    {
        $this->add('GET', $uri, $action, $name);
    }

    public function post(string $uri, mixed $action, ?string $name = null): void
    {
        $this->add('POST', $uri, $action, $name);
    }

    private function add(string $method, string $uri, mixed $action, ?string $name): void
    {
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static fn (array $match): string => '(?P<' . $match[1] . '>[^/]+)',
            $uri
        );

        if ($pattern === null) {
            throw new RuntimeException('Invalid route pattern: ' . $uri);
        }

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'pattern' => '#^' . $pattern . '$#u',
            'action' => $action,
            'name' => $name,
        ];

        if ($name !== null) {
            $this->names[$name] = $uri;
        }
    }

    public function dispatch(Request $request): void
    {
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $request->path, $matches)) {
                continue;
            }

            $pathMatched = true;
            if ($route['method'] !== $request->method) {
                continue;
            }

            $parameters = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $parameters[$key] = $value;
                }
            }

            $this->invoke($route['action'], $parameters);
            return;
        }

        if ($pathMatched) {
            http_response_code(405);
            header('Allow: GET, POST');
            echo view('errors/405', ['meta' => ['title' => 'Méthode non autorisée']]);
            return;
        }

        http_response_code(404);
        echo view('errors/404', ['meta' => ['title' => 'Page introuvable']]);
    }

    /** @param array<string, string> $parameters */
    private function invoke(mixed $action, array $parameters): void
    {
        if ($action instanceof Closure) {
            $action($parameters);
            return;
        }

        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;
            if (!class_exists($class)) {
                throw new RuntimeException('Controller not found: ' . $class);
            }
            $controller = new $class();
            if (!method_exists($controller, $method)) {
                throw new RuntimeException('Controller action not found: ' . $class . '::' . $method);
            }
            $controller->{$method}(...$parameters);
            return;
        }

        throw new RuntimeException('Unsupported route action.');
    }

    /** @param array<string, scalar> $parameters */
    public function urlFor(string $name, array $parameters = []): string
    {
        if (!array_key_exists($name, $this->names)) {
            throw new RuntimeException('Unknown route name: ' . $name);
        }

        $uri = $this->names[$name];
        foreach ($parameters as $key => $value) {
            $uri = str_replace('{' . $key . '}', rawurlencode((string) $value), $uri);
        }

        if (preg_match('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', $uri)) {
            throw new RuntimeException('Missing route parameter for: ' . $name);
        }

        return $uri;
    }
}
