<?php

declare(strict_types=1);

namespace App\Controllers;

final class LegalController extends Controller
{
    public function privacy(): void
    {
        $this->render('legal/privacy', [], [
            'title' => 'Confidentialité & données personnelles — Brazza Hotel',
            'description' => 'Comment Brazza Hotel collecte et traite les données transmises via les formulaires de contact et de réservation.',
            'canonical' => route('legal.privacy'),
        ]);
    }

    public function legal(): void
    {
        $this->render('legal/mentions', [], [
            'title' => 'Mentions légales — Brazza Hotel',
            'description' => 'Mentions légales du site Brazza Hotel. Informations à compléter par l’établissement.',
            'canonical' => route('legal.mentions'),
        ]);
    }
}
