<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Plain-PHP view renderer. Views are isolated: variables are local to the
 * template and the rendered body is injected into the requested layout.
 */
final class View
{
    public function __construct(private readonly string $viewPath)
    {
    }

    /** @param array<string, mixed> $data */
    public function render(string $view, array $data = [], ?string $layout = 'layouts/app'): string
    {
        $data['meta'] = array_merge([
            'title' => 'Brazza Hotel',
            'description' => 'Brazza Hotel, Brazzaville, République du Congo.',
            'image' => asset('assets/images/hero.jpg'),
            'canonical' => url(ltrim(request()->path, '/')),
            'robots' => 'index, follow',
        ], $data['meta'] ?? []);

        $content = $this->renderFile($view, $data);

        if ($layout === null) {
            return $content;
        }

        $data['content'] = $content;
        return $this->renderFile($layout, $data);
    }

    /** @param array<string, mixed> $data */
    public function partial(string $view, array $data = []): void
    {
        echo $this->renderFile($view, $data);
    }

    /** @param array<string, mixed> $data */
    private function renderFile(string $view, array $data): string
    {
        $file = $this->viewPath . '/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        try {
            require $file;
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }
        $output = ob_get_clean();

        return $output === false ? '' : $output;
    }
}
