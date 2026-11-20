<?php

declare(strict_types=1);

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\GalleryAdminController;
use App\Controllers\Admin\MessageAdminController;
use App\Controllers\Admin\ProfileAdminController;
use App\Controllers\Admin\ReservationAdminController;
use App\Controllers\Admin\RoomAdminController;
use App\Controllers\Admin\SettingsAdminController;

/** @var App\Core\Router $router */
$router = app()->router();

// Authentication --------------------------------------------------------------
$router->get('/admin/connexion', [AuthController::class, 'login'], 'admin.login');
$router->post('/admin/connexion', [AuthController::class, 'authenticate'], 'admin.authenticate');
$router->post('/admin/deconnexion', [AuthController::class, 'logout'], 'admin.logout');

// Dashboard -------------------------------------------------------------------
$router->get('/admin', [DashboardController::class, 'index'], 'admin.dashboard');

// Reservations ----------------------------------------------------------------
$router->get('/admin/reservations', [ReservationAdminController::class, 'index'], 'admin.reservations.index');
$router->get('/admin/reservations/{id}', [ReservationAdminController::class, 'show'], 'admin.reservations.show');
$router->post('/admin/reservations/{id}/statut', [ReservationAdminController::class, 'updateStatus'], 'admin.reservations.status');

// Rooms -----------------------------------------------------------------------
$router->get('/admin/chambres', [RoomAdminController::class, 'index'], 'admin.rooms.index');
$router->get('/admin/chambres/creer', [RoomAdminController::class, 'create'], 'admin.rooms.create');
$router->post('/admin/chambres', [RoomAdminController::class, 'store'], 'admin.rooms.store');
$router->get('/admin/chambres/{id}/modifier', [RoomAdminController::class, 'edit'], 'admin.rooms.edit');
$router->post('/admin/chambres/{id}', [RoomAdminController::class, 'update'], 'admin.rooms.update');
$router->post('/admin/chambres/{id}/supprimer', [RoomAdminController::class, 'delete'], 'admin.rooms.delete');
$router->post('/admin/chambres/{id}/images', [RoomAdminController::class, 'uploadImages'], 'admin.rooms.images.store');
$router->post('/admin/chambres/images/{imageId}/supprimer', [RoomAdminController::class, 'deleteImage'], 'admin.rooms.images.delete');

// Gallery ---------------------------------------------------------------------
$router->get('/admin/galerie', [GalleryAdminController::class, 'index'], 'admin.gallery.index');
$router->post('/admin/galerie', [GalleryAdminController::class, 'store'], 'admin.gallery.store');
$router->post('/admin/galerie/{id}/supprimer', [GalleryAdminController::class, 'delete'], 'admin.gallery.delete');
$router->post('/admin/galerie/categories', [GalleryAdminController::class, 'createCategory'], 'admin.gallery.categories.store');

// Messages --------------------------------------------------------------------
$router->get('/admin/messages', [MessageAdminController::class, 'index'], 'admin.messages.index');
$router->get('/admin/messages/{id}', [MessageAdminController::class, 'show'], 'admin.messages.show');
$router->post('/admin/messages/{id}/statut', [MessageAdminController::class, 'updateStatus'], 'admin.messages.status');

// Settings & account ----------------------------------------------------------
$router->get('/admin/parametres', [SettingsAdminController::class, 'index'], 'admin.settings.index');
$router->post('/admin/parametres', [SettingsAdminController::class, 'update'], 'admin.settings.update');
$router->get('/admin/profil', [ProfileAdminController::class, 'index'], 'admin.profile.index');
$router->post('/admin/profil', [ProfileAdminController::class, 'update'], 'admin.profile.update');
$router->post('/admin/profil/mot-de-passe', [ProfileAdminController::class, 'updatePassword'], 'admin.profile.password');
