<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Services\Auth;
use App\Services\RateLimiter;

final class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('admin.dashboard');
        }

        $this->renderLogin();
    }

    public function authenticate(): void
    {
        verify_csrf();

        if (RateLimiter::tooMany('admin_login', 5, 600)) {
            Session::flash('error', 'Trop de tentatives. Merci de patienter quelques minutes avant de réessayer.');
            $this->redirectRoute('admin.login');
        }

        $email = request()->string('email');
        $password = (string) request()->input('password', '');

        $errors = Validator::validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email|max:190', 'password' => 'required|min:6|max:200']
        );

        if ($errors !== []) {
            Session::flash('_errors', $errors);
            Session::flash('error', 'Identifiants invalides.');
            Session::flashInput(['email' => $email]);
            RateLimiter::hit('admin_login', 600);
            $this->redirectRoute('admin.login');
        }

        if (!Auth::attempt($email, $password)) {
            RateLimiter::hit('admin_login', 600);
            Session::flash('error', 'Identifiants invalides.');
            Session::flashInput(['email' => $email]);
            // Small constant-like delay limits trivial user enumeration/brute force.
            usleep(random_int(250000, 450000));
            $this->redirectRoute('admin.login');
        }

        RateLimiter::clear('admin_login');
        Session::rotateCsrf();
        Session::flash('success', 'Bienvenue dans l’espace d’administration.');

        $intended = Session::get('intended_url');
        Session::forget('intended_url');
        if (is_string($intended) && str_starts_with($intended, '/admin') && !str_contains($intended, '//')) {
            $this->redirect($intended);
        }

        $this->redirectRoute('admin.dashboard');
    }

    public function logout(): void
    {
        verify_csrf();
        Auth::logout();
        Session::flash('success', 'Vous êtes déconnecté.');
        $this->redirectRoute('admin.login');
    }

    private function renderLogin(): void
    {
        echo view('admin/auth/login', [
            'meta' => ['title' => 'Connexion — Espace Brazza Hotel', 'robots' => 'noindex, nofollow'],
        ], 'layouts/admin-auth');
    }
}
