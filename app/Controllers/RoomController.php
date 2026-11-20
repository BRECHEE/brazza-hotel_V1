<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Room;

final class RoomController extends Controller
{
    public function index(): void
    {
        $this->render('rooms/index', [
            'rooms' => Room::allActive(),
        ], [
            'title' => 'Chambres & suites — Brazza Hotel, Brazzaville',
            'description' => 'Découvrez les catégories de chambres présentées par Brazza Hotel à Brazzaville. Tarifs sur demande, disponibilités confirmées par l’équipe.',
            'canonical' => route('rooms.index'),
        ]);
    }

    public function show(string $slug): void
    {
        $room = Room::findBySlug($slug);
        if ($room === null) {
            $this->notFound();
        }

        $images = Room::images((int) $room['id']);
        $amenities = Room::amenities((int) $room['id']);
        $heroImage = $images[0]['file_path'] ?? 'assets/images/placeholder.svg';

        $this->render('rooms/show', [
            'room' => $room,
            'images' => $images,
            'amenities' => $amenities,
            'heroImage' => $heroImage,
        ], [
            'title' => ($room['meta_title'] ?: $room['name'] . ' — Brazza Hotel'),
            'description' => ($room['meta_description'] ?: excerpt($room['short_description'], 155)),
            'image' => media_url($heroImage),
            'canonical' => route('rooms.show', ['slug' => $room['slug']]),
        ]);
    }
}
