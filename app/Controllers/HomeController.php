<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Gallery;
use App\Models\Room;
use App\Models\Service;

final class HomeController extends Controller
{
    public function index(): void
    {
        $rooms = Room::featured(3);
        if ($rooms === []) {
            $rooms = array_slice(Room::allActive(), 0, 3);
        }

        $this->render('home/index', [
            'rooms' => $rooms,
            'services' => Service::active(4),
            'gallery' => Gallery::featured(6),
        ], [
            'title' => 'Brazza Hotel — Hôtel à Brazzaville, République du Congo',
            'description' => 'Brazza Hotel, une adresse contemporaine à Brazzaville. Découvrez les chambres, la table et l’art de recevoir. Demande de réservation en ligne.',
            'image' => asset('assets/images/og-brazza-hotel.jpg'),
            'canonical' => route('home'),
        ]);
    }
}
