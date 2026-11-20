<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Models\Setting;

final class SettingsAdminController extends AdminController
{
    private const GROUP_LABELS = [
        'identity' => 'Identité de l’hôtel',
        'contact' => 'Coordonnées',
        'stay' => 'Séjour & horaires',
        'restaurant' => 'Restaurant',
        'booking' => 'Réservation',
        'social' => 'Réseaux sociaux',
    ];

    public function index(): void
    {
        $rows = Database::connection()->query(
            'SELECT * FROM hotel_settings ORDER BY FIELD(setting_group, "identity", "contact", "stay", "restaurant", "booking", "social"), setting_key'
        )->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(string) $row['setting_group']][] = $row;
        }

        $this->renderAdmin('admin/settings/index', 'Informations du site', [
            'groupedSettings' => $grouped,
            'groupLabels' => self::GROUP_LABELS,
        ]);
    }

    public function update(): void
    {
        verify_csrf();

        $rows = Database::connection()->query(
            'SELECT setting_key, input_type, is_public FROM hotel_settings'
        )->fetchAll();
        $values = [];
        $verifiedInput = request()->input('verified', []);
        $verifiedInput = is_array($verifiedInput) ? $verifiedInput : [];
        $errors = [];

        foreach ($rows as $row) {
            $key = (string) $row['setting_key'];
            if (!array_key_exists($key, request()->body)) {
                continue;
            }

            $value = request()->string($key);
            $type = (string) $row['input_type'];
            $max = $type === 'textarea' ? 5000 : 500;

            if (mb_strlen($value) > $max) {
                $errors[$key] = 'La valeur de ce champ est trop longue.';
                continue;
            }

            if ($value !== '') {
                if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$key] = 'Adresse e-mail invalide.';
                }
                if ($type === 'url') {
                    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
                    if (!filter_var($value, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                        $errors[$key] = 'Adresse web invalide (http ou https requis).';
                    }
                }
                if ($type === 'phone' && !preg_match('/^\+?[0-9\s().-]{6,40}$/', $value)) {
                    $errors[$key] = 'Numéro de téléphone invalide.';
                }
                if ($type === 'time' && !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
                    $errors[$key] = 'Heure invalide (format HH:MM).';
                }
            }

            $values[$key] = $value;
        }

        if ($errors !== []) {
            $this->backWithErrors($errors, request()->body, '/admin/parametres');
        }

        $verified = [];
        foreach ($verifiedInput as $key => $enabled) {
            if (is_string($key) && $enabled) {
                $verified[] = $key;
            }
        }

        Setting::updateMany($values, $verified);
        $this->audit('settings.updated', 'settings', null, count($values) . ' valeurs');
        Session::flash('success', 'Les informations du site ont été mises à jour.');
        $this->redirectRoute('admin.settings.index');
    }
}
