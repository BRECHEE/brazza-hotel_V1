<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Gallery;

final class GalleryController extends Controller
{
    public function index(): void
    {
        $categories = Gallery::categories();
        $requested = request()->string('categorie');
        $validSlugs = array_column($categories, 'slug');
        $active = in_array($requested, $validSlugs, true) ? $requested : null;

        $this->render('gallery/index', [
            'categories' => $categories,
            'images' => Gallery::images($active),
            'activeCategory' => $active,
            'totalImages' => Gallery::countActive(),
        ], [
            'title' => 'Galerie — Brazza Hotel, Brazzaville',
            'description' => 'Parcourez la galerie éditoriale du Brazza Hotel. Visuels d’illustration clairement identifiés, en attendant les photographies officielles.',
            'canonical' => route('gallery.index'),
        ]);
    }
}
