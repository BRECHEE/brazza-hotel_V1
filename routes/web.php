<?php

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\ContactController;
use App\Controllers\GalleryController;
use App\Controllers\HomeController;
use App\Controllers\LegalController;
use App\Controllers\ReservationController;
use App\Controllers\RestaurantController;
use App\Controllers\RoomController;
use App\Controllers\SeoController;

/** @var App\Core\Router $router */
$router = app()->router();

// Public site -----------------------------------------------------------------
$router->get('/', [HomeController::class, 'index'], 'home');

$router->get('/chambres', [RoomController::class, 'index'], 'rooms.index');
$router->get('/chambres/{slug}', [RoomController::class, 'show'], 'rooms.show');

$router->get('/restaurant', [RestaurantController::class, 'index'], 'restaurant.index');
$router->get('/galerie', [GalleryController::class, 'index'], 'gallery.index');
$router->get('/a-propos', [AboutController::class, 'index'], 'about.index');

$router->get('/contact', [ContactController::class, 'index'], 'contact.index');
$router->post('/contact', [ContactController::class, 'store'], 'contact.store');

$router->get('/reservation', [ReservationController::class, 'create'], 'reservation.create');
$router->post('/reservation', [ReservationController::class, 'store'], 'reservation.store');
$router->get('/reservation/merci', [ReservationController::class, 'confirmation'], 'reservation.confirmation');

$router->get('/confidentialite', [LegalController::class, 'privacy'], 'legal.privacy');
$router->get('/mentions-legales', [LegalController::class, 'legal'], 'legal.mentions');

// SEO -------------------------------------------------------------------------
$router->get('/sitemap.xml', [SeoController::class, 'sitemap'], 'sitemap');
$router->get('/robots.txt', [SeoController::class, 'robots'], 'robots');

// Administration --------------------------------------------------------------
require __DIR__ . '/admin.php';
