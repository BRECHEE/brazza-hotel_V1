<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Session;
use App\Services\Auth;

abstract class AdminController extends Controller
{
    /** @var array<string, mixed> */
    protected array $admin;

    public function __construct()
    {
        if (!Auth::check()) {
            Session::flash('error', 'Veuillez vous connecter pour accéder à l’administration.');
            Session::put('intended_url', request()->path);
            $this->redirectRoute('admin.login');
        }

        $lastSeen = Session::get('admin_last_seen');
        $lifetime = (int) config('app.session.lifetime', 7200);
        if (is_int($lastSeen) && (time() - $lastSeen) > $lifetime) {
            Auth::logout();
            Session::flash('error', 'Votre session a expiré. Merci de vous reconnecter.');
            $this->redirectRoute('admin.login');
        }

        Session::put('admin_last_seen', time());
        header('Cache-Control: no-store, private, max-age=0');
        header('X-Robots-Tag: noindex, nofollow');
        $this->admin = (array) Auth::user();
    }

    /** @param array<string, mixed> $data */
    protected function renderAdmin(string $view, string $pageTitle, array $data = []): void
    {
        echo view($view, array_merge($data, [
            'pageTitle' => $pageTitle,
            'adminUser' => $this->admin,
        ]), 'layouts/admin');
    }

    protected function audit(string $action, ?string $targetType = null, ?int $targetId = null, ?string $context = null): void
    {
        \App\Services\ActivityLog::record($action, $targetType, $targetId, $context);
    }
}
