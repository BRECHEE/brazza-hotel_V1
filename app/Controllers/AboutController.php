<?php

declare(strict_types=1);

namespace App\Controllers;

final class AboutController extends Controller
{
    public function index(): void
    {
        $this->render('about/index', [], [
            'title' => 'À propos — Brazza Hotel, Brazzaville',
            'description' => 'Découvrez la présentation du Brazza Hotel à Brazzaville. Histoire, philosophie et engagements à confirmer auprès de l’établissement.',
            'image' => asset('assets/images/about.jpg'),
            'canonical' => route('about.index'),
        ]);
    }
}
