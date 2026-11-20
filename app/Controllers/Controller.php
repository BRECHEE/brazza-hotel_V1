<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Services\Auth;

abstract class Controller
{
    /** @param array<string, mixed> $data @param array<string, mixed> $meta */
    protected function render(string $view, array $data = [], array $meta = [], string $layout = 'layouts/app'): void
    {
        echo view($view, array_merge($data, ['meta' => $meta]), $layout);
    }

    protected function redirect(string $path, int $status = 303): never
    {
        header('Location: ' . url($path), true, $status);
        exit;
    }

    /** @param array<string, scalar> $parameters */
    protected function redirectRoute(string $name, array $parameters = [], int $status = 303): never
    {
        $this->redirect(app()->router()->urlFor($name, $parameters), $status);
    }

    /** @param array<string, string> $errors @param array<string, mixed> $input */
    protected function backWithErrors(array $errors, array $input, string $fallback): never
    {
        Session::flash('_errors', $errors);
        Session::flashInput($input);
        Session::flash('error', 'Merci de vérifier les champs signalés.');
        $this->redirect($fallback);
    }

    protected function notFound(string $message = 'La page demandée n’existe pas.'): never
    {
        http_response_code(404);
        echo view('errors/404', [
            'meta' => ['title' => 'Page introuvable', 'robots' => 'noindex, nofollow'],
            'message' => $message,
        ]);
        exit;
    }

    protected function requireAdmin(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Veuillez vous connecter pour accéder à l’administration.');
            Session::put('intended_url', request()->path);
            $this->redirectRoute('admin.login');
        }

        header('Cache-Control: no-store, private');
    }

    /** @return array<string, mixed> */
    protected function admin(): array
    {
        $admin = Auth::user();
        if ($admin === null) {
            $this->redirectRoute('admin.login');
        }
        return $admin;
    }
}
