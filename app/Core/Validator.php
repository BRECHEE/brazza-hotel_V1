<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Server-side validator for the small set of rules this site needs.
 *
 * @return array<string, string> field => human-readable French error
 */
final class Validator
{
    /** @param array<string, mixed> $data @param array<string, string> $rules */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $string = is_scalar($value) ? trim((string) $value) : '';
            $label = self::label($field);

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required' && $string === '') {
                    $errors[$field] = $label . ' est obligatoire.';
                    break;
                }

                if ($string === '') {
                    continue;
                }

                switch ($name) {
                    case 'min':
                        if (mb_strlen($string) < (int) $argument) {
                            $errors[$field] = sprintf('%s doit contenir au moins %d caractères.', $label, (int) $argument);
                        }
                        break;
                    case 'max':
                        if (mb_strlen($string) > (int) $argument) {
                            $errors[$field] = sprintf('%s ne peut pas dépasser %d caractères.', $label, (int) $argument);
                        }
                        break;
                    case 'email':
                        if (!filter_var($string, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Veuillez saisir une adresse e-mail valide.';
                        }
                        break;
                    case 'phone':
                        $digits = preg_replace('/\D+/', '', $string) ?? '';
                        if (!preg_match('/^\+?[0-9\s().-]{6,30}$/', $string) || strlen($digits) < 6) {
                            $errors[$field] = 'Veuillez saisir un numéro de téléphone valide.';
                        }
                        break;
                    case 'date':
                        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $string);
                        if (!$date || $date->format('Y-m-d') !== $string) {
                            $errors[$field] = 'Veuillez saisir une date valide.';
                        }
                        break;
                    case 'in':
                        $allowed = array_map('trim', explode(',', (string) $argument));
                        if (!in_array($string, $allowed, true)) {
                            $errors[$field] = 'La valeur sélectionnée est invalide.';
                        }
                        break;
                    case 'slug':
                        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $string)) {
                            $errors[$field] = 'Le format du lien est invalide.';
                        }
                        break;
                }

                if (isset($errors[$field])) {
                    break;
                }
            }
        }

        return $errors;
    }

    private static function label(string $field): string
    {
        return [
            'name' => 'Le nom',
            'full_name' => 'Le nom complet',
            'email' => 'L’adresse e-mail',
            'phone' => 'Le téléphone',
            'subject' => 'Le sujet',
            'message' => 'Le message',
            'check_in' => 'La date d’arrivée',
            'check_out' => 'La date de départ',
            'guests' => 'Le nombre de voyageurs',
            'room_id' => 'La catégorie de chambre',
            'password' => 'Le mot de passe',
            'title' => 'Le titre',
            'description' => 'La description',
            'slug' => 'Le lien',
            'alt_text' => 'Le texte alternatif',
            'icon' => 'L’icône',
        ][$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
