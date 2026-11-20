<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Core\Validator;
use App\Models\Admin;
use App\Services\Auth;

final class ProfileAdminController extends AdminController
{
    public function index(): void
    {
        $this->renderAdmin('admin/profile/index', 'Mon compte', [
            'profile' => $this->admin,
        ]);
    }

    public function update(): void
    {
        verify_csrf();
        $name = request()->string('name');
        $email = request()->string('email');

        $errors = Validator::validate(
            ['name' => $name, 'email' => $email],
            ['name' => 'required|min:2|max:120', 'email' => 'required|email|max:190']
        );

        if ($email !== '' && Admin::emailExists($email, (int) $this->admin['id'])) {
            $errors['email'] = 'Cette adresse e-mail est déjà associée à un autre compte.';
        }

        if ($errors !== []) {
            $this->backWithErrors($errors, ['name' => $name, 'email' => $email], '/admin/profil');
        }

        try {
            Admin::updateProfile((int) $this->admin['id'], $name, $email);
            $updated = Admin::find((int) $this->admin['id']);
            if ($updated !== null) {
                Auth::setUser($updated);
            }
            $this->audit('admin.profile_updated', 'admin', (int) $this->admin['id']);
            Session::flash('success', 'Votre profil a été mis à jour.');
        } catch (\Throwable $exception) {
            error_log('[admin-profile] ' . $exception->getMessage());
            Session::flash('error', 'Impossible de mettre à jour votre profil.');
        }

        $this->redirectRoute('admin.profile.index');
    }

    public function updatePassword(): void
    {
        verify_csrf();
        $current = (string) request()->input('current_password', '');
        $new = (string) request()->input('new_password', '');
        $confirm = (string) request()->input('confirm_password', '');

        $errors = Validator::validate(
            ['new_password' => $new],
            ['new_password' => 'required|min:12|max:200']
        );

        if ($new !== $confirm) {
            $errors['confirm_password'] = 'Les deux nouveaux mots de passe ne correspondent pas.';
        }

        $credentials = Admin::findCredentials((int) $this->admin['id']);
        if ($credentials === null || !password_verify($current, (string) $credentials['password_hash'])) {
            $errors['current_password'] = 'Le mot de passe actuel est incorrect.';
        }

        if ($errors !== []) {
            Session::flash('_errors', $errors);
            Session::flash('error', 'Le mot de passe n’a pas été modifié.');
            $this->redirectRoute('admin.profile.index');
        }

        Admin::updatePassword((int) $this->admin['id'], $new);
        Session::regenerate();
        Session::rotateCsrf();
        $this->audit('admin.password_updated', 'admin', (int) $this->admin['id']);
        Session::flash('success', 'Votre mot de passe a été modifié.');
        $this->redirectRoute('admin.profile.index');
    }
}
