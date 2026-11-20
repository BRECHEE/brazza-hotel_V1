<?php

declare(strict_types=1);

namespace App\Controllers;

final class RestaurantController extends Controller
{
    public function index(): void
    {
        $this->render('restaurant/index', [], [
            'title' => 'Restaurant — Brazza Hotel, Brazzaville',
            'description' => 'Aperçu de la table du Brazza Hotel à Brazzaville. Carte, horaires et services à confirmer avec l’établissement.',
            'image' => asset('assets/images/og-brazza-hotel.jpg'),
            'canonical' => route('restaurant.index'),
        ]);
    }
}
