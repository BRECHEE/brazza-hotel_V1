<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Core\Validator;
use App\Models\Room;
use App\Services\UploadService;

final class RoomAdminController extends AdminController
{
    public function index(): void
    {
        $this->renderAdmin('admin/rooms/index', 'Gestion des chambres', [
            'rooms' => Room::adminAll(),
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('admin/rooms/form', 'Nouvelle catégorie', [
            'room' => null,
            'images' => [],
            'formAction' => route('admin.rooms.store'),
        ]);
    }

    public function store(): void
    {
        verify_csrf();
        [$data, $errors] = $this->validatedPayload();

        if ($errors !== []) {
            $this->backWithErrors($errors, request()->body, '/admin/chambres/creer');
        }

        try {
            $id = Room::create($data);
            $this->audit('room.created', 'room', $id, (string) $data['name']);
            Session::flash('success', 'La catégorie a été créée. Vous pouvez maintenant ajouter des photographies.');
            $this->redirectRoute('admin.rooms.edit', ['id' => $id]);
        } catch (\Throwable $exception) {
            error_log('[admin-room-create] ' . $exception->getMessage());
            Session::flash('error', 'Impossible de créer la catégorie. Vérifiez que le lien est unique.');
            Session::flashInput(request()->body);
            $this->redirectRoute('admin.rooms.create');
        }
    }

    public function edit(string $id): void
    {
        $room = Room::adminFind((int) $id);
        if ($room === null) {
            Session::flash('error', 'Cette catégorie est introuvable.');
            $this->redirectRoute('admin.rooms.index');
        }

        $this->renderAdmin('admin/rooms/form', 'Modifier — ' . $room['name'], [
            'room' => $room,
            'images' => Room::images((int) $id),
            'formAction' => route('admin.rooms.update', ['id' => (int) $id]),
        ]);
    }

    public function update(string $id): void
    {
        verify_csrf();
        $room = Room::adminFind((int) $id);
        if ($room === null) {
            Session::flash('error', 'Cette catégorie est introuvable.');
            $this->redirectRoute('admin.rooms.index');
        }

        [$data, $errors] = $this->validatedPayload((int) $id);
        if ($errors !== []) {
            $this->backWithErrors($errors, request()->body, '/admin/chambres/' . (int) $id . '/modifier');
        }

        try {
            Room::update((int) $id, $data);
            $this->audit('room.updated', 'room', (int) $id, (string) $data['name']);
            Session::flash('success', 'La catégorie a été mise à jour.');
            $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
        } catch (\Throwable $exception) {
            error_log('[admin-room-update] ' . $exception->getMessage());
            Session::flash('error', 'Impossible de mettre à jour la catégorie.');
            Session::flashInput(request()->body);
            $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
        }
    }

    public function uploadImages(string $id): void
    {
        verify_csrf();
        $room = Room::adminFind((int) $id);
        if ($room === null) {
            Session::flash('error', 'Cette catégorie est introuvable.');
            $this->redirectRoute('admin.rooms.index');
        }

        $files = $this->normaliseFiles(request()->file('images'));
        if ($files === []) {
            Session::flash('error', 'Sélectionnez au moins une image.');
            $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
        }

        $existing = count(Room::images((int) $id));
        if ($existing + count($files) > 10) {
            Session::flash('error', 'Une catégorie ne peut pas contenir plus de 10 photographies.');
            $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
        }

        $alt = request()->string('alt_text');
        if (mb_strlen($alt) > 180) {
            $alt = mb_substr($alt, 0, 180);
        }
        if ($alt === '') {
            $alt = 'Photographie de ' . $room['name'];
        }

        $primary = request()->boolean('is_primary');
        $storedPaths = [];
        $pdo = \App\Core\Database::connection();

        try {
            foreach ($files as $file) {
                $storedPaths[] = UploadService::storeImage($file, 'rooms');
            }

            $pdo->beginTransaction();
            foreach ($storedPaths as $index => $path) {
                Room::addImage((int) $id, $path, $alt, $primary && $index === 0 && $existing === 0);
            }
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            foreach ($storedPaths as $stored) {
                UploadService::delete($stored);
            }
            Session::flash('error', 'Téléversement refusé : ' . $exception->getMessage());
            $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
        }

        $count = count($storedPaths);
        $this->audit('room.images_uploaded', 'room', (int) $id, $count . ' image(s)');
        Session::flash('success', $count . ' photographie(s) ajoutée(s).');
        $this->redirectRoute('admin.rooms.edit', ['id' => (int) $id]);
    }

    public function deleteImage(string $imageId): void
    {
        verify_csrf();
        $image = Room::findImage((int) $imageId);
        if ($image === null) {
            Session::flash('error', 'Cette image est introuvable.');
            $this->redirectRoute('admin.rooms.index');
        }

        UploadService::delete((string) $image['file_path']);
        Room::deleteImage((int) $imageId);
        $this->audit('room.image_deleted', 'room', (int) $image['room_id']);
        Session::flash('success', 'La photographie a été supprimée.');
        $this->redirectRoute('admin.rooms.edit', ['id' => (int) $image['room_id']]);
    }

    public function delete(string $id): void
    {
        verify_csrf();
        $room = Room::adminFind((int) $id);
        if ($room === null) {
            Session::flash('error', 'Cette catégorie est introuvable.');
            $this->redirectRoute('admin.rooms.index');
        }

        foreach (Room::images((int) $id) as $image) {
            UploadService::delete((string) $image['file_path']);
        }
        Room::delete((int) $id);
        $this->audit('room.deleted', 'room', (int) $id, (string) $room['name']);
        Session::flash('success', 'La catégorie a été supprimée.');
        $this->redirectRoute('admin.rooms.index');
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    private function validatedPayload(?int $exceptId = null): array
    {
        $input = request()->body;
        $name = request()->string('name');
        $slug = request()->string('slug');
        $slug = $slug !== '' ? slugify($slug) : slugify($name);

        $errors = Validator::validate([
            'name' => $name,
            'slug' => $slug,
            'short_description' => request()->string('short_description'),
            'description' => request()->string('description'),
            'price' => request()->string('price_per_night'),
            'max_occupancy' => request()->string('max_occupancy'),
            'content_status' => request()->string('content_status'),
        ], [
            'name' => 'required|min:2|max:120',
            'slug' => 'required|slug|max:160',
            'short_description' => 'required|min:10|max:400',
            'description' => 'required|min:20',
            'max_occupancy' => 'required|max:2',
            'content_status' => 'required|in:demo,verified',
        ]);

        if ($slug !== '' && Room::slugExists($slug, $exceptId)) {
            $errors['slug'] = 'Ce lien est déjà utilisé par une autre catégorie.';
        }

        $price = null;
        $priceRaw = trim(str_replace([' ', ','], ['', '.'], request()->string('price_per_night')));
        if ($priceRaw !== '') {
            if (!is_numeric($priceRaw) || (float) $priceRaw < 0 || (float) $priceRaw > 999999999) {
                $errors['price'] = 'Indiquez un tarif valide ou laissez vide.';
            } else {
                $price = number_format((float) $priceRaw, 2, '.', '');
            }
        }

        $occupancy = filter_var(request()->input('max_occupancy'), FILTER_VALIDATE_INT);
        if ($occupancy === false || $occupancy < 1 || $occupancy > 20) {
            $errors['max_occupancy'] = 'La capacité doit être comprise entre 1 et 20.';
        }

        $size = request()->string('size_sqm');
        if ($size !== '' && (!ctype_digit($size) || (int) $size < 1 || (int) $size > 2000)) {
            $errors['size_sqm'] = 'La superficie doit être un nombre valide.';
        }

        $data = [
            'slug' => $slug,
            'name' => $name,
            'tagline' => request()->string('tagline') !== '' ? request()->string('tagline') : null,
            'short_description' => request()->string('short_description'),
            'description' => request()->string('description'),
            'price' => $price,
            'currency' => 'XAF',
            'max_occupancy' => (int) ($occupancy ?: 2),
            'bed_type' => request()->string('bed_type') !== '' ? request()->string('bed_type') : null,
            'size_sqm' => $size !== '' ? (int) $size : null,
            'is_featured' => request()->boolean('is_featured') ? 1 : 0,
            'is_active' => request()->boolean('is_active') ? 1 : 0,
            'content_status' => request()->string('content_status'),
            'sort_order' => max(0, min(999, request()->integer('sort_order'))),
            'meta_title' => request()->string('meta_title') !== '' ? request()->string('meta_title') : null,
            'meta_description' => request()->string('meta_description') !== '' ? request()->string('meta_description') : null,
        ];

        return [$data, $errors];
    }

    /**
     * @param array<string, mixed> $file
     * @return array<int, array<string, mixed>>
     */
    private function normaliseFiles(array $file): array
    {
        if (!isset($file['name'])) {
            return [];
        }
        if (!is_array($file['name'])) {
            return [$file];
        }

        $files = [];
        foreach (array_keys($file['name']) as $index) {
            $files[] = [
                'name' => $file['name'][$index],
                'type' => $file['type'][$index] ?? '',
                'tmp_name' => $file['tmp_name'][$index] ?? '',
                'error' => $file['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $file['size'][$index] ?? 0,
            ];
        }
        return array_values(array_filter(
            $files,
            static fn (array $item): bool => (int) ($item['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
        ));
    }
}
