<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Gallery;
use App\Services\UploadService;

final class GalleryAdminController extends AdminController
{
    public function index(): void
    {
        $this->renderAdmin('admin/gallery/index', 'Galerie', [
            'images' => Gallery::adminAll(),
            'categories' => Gallery::categories(),
        ]);
    }

    public function store(): void
    {
        verify_csrf();

        $alt = request()->string('alt_text');
        $caption = request()->string('caption');
        $categoryId = request()->integer('category_id');
        $contentStatus = request()->string('content_status', 'demo');

        $errors = Validator::validate([
            'alt_text' => $alt,
            'caption' => $caption,
            'content_status' => $contentStatus,
        ], [
            'alt_text' => 'required|min:3|max:180',
            'caption' => 'max:200',
            'content_status' => 'required|in:demo,verified',
        ]);

        $categoryValid = false;
        if ($categoryId > 0) {
            $statement = Database::connection()->prepare('SELECT COUNT(*) FROM gallery_categories WHERE id = :id');
            $statement->execute(['id' => $categoryId]);
            $categoryValid = (int) $statement->fetchColumn() === 1;
        }
        if (!$categoryValid) {
            $errors['category_id'] = 'Sélectionnez une catégorie valide.';
        }

        $file = request()->file('image');
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $errors['image'] = 'Sélectionnez une image.';
        }

        if ($errors !== []) {
            $this->backWithErrors($errors, request()->body, '/admin/galerie');
        }

        try {
            $path = UploadService::storeImage($file, 'gallery');
            Gallery::create([
                'category_id' => $categoryId,
                'file_path' => $path,
                'alt_text' => $alt,
                'caption' => $caption !== '' ? $caption : null,
                'is_featured' => request()->boolean('is_featured') ? 1 : 0,
                'is_active' => request()->boolean('is_active') ? 1 : 0,
                'content_status' => $contentStatus,
                'sort_order' => max(0, min(999, request()->integer('sort_order'))),
            ]);
            $this->audit('gallery.image_uploaded', 'gallery', null, $alt);
            Session::flash('success', 'L’image a été ajoutée à la galerie.');
        } catch (\Throwable $exception) {
            if (isset($path)) {
                UploadService::delete($path);
            }
            error_log('[admin-gallery-store] ' . $exception->getMessage());
            Session::flash('error', 'Impossible d’ajouter l’image : ' . $exception->getMessage());
        }

        $this->redirectRoute('admin.gallery.index');
    }

    public function delete(string $id): void
    {
        verify_csrf();
        $image = Gallery::find((int) $id);
        if ($image === null) {
            Session::flash('error', 'Cette image est introuvable.');
            $this->redirectRoute('admin.gallery.index');
        }

        UploadService::delete((string) $image['file_path']);
        Gallery::delete((int) $id);
        $this->audit('gallery.image_deleted', 'gallery', (int) $id);
        Session::flash('success', 'L’image a été supprimée.');
        $this->redirectRoute('admin.gallery.index');
    }

    public function createCategory(): void
    {
        verify_csrf();
        $name = request()->string('name');
        $slug = request()->string('slug');
        $slug = $slug !== '' ? slugify($slug) : slugify($name);

        $errors = Validator::validate(
            ['name' => $name, 'slug' => $slug],
            ['name' => 'required|min:2|max:120', 'slug' => 'required|slug|max:120']
        );
        if ($slug !== '' && Gallery::categorySlugExists($slug)) {
            $errors['slug'] = 'Ce lien de catégorie est déjà utilisé.';
        }

        if ($errors !== []) {
            Session::flash('_errors', $errors);
            Session::flash('error', 'La catégorie n’a pas pu être créée.');
            Session::flashInput(request()->body);
            $this->redirectRoute('admin.gallery.index');
        }

        $id = Gallery::createCategory($name, $slug);
        $this->audit('gallery.category_created', 'gallery_category', $id, $name);
        Session::flash('success', 'La catégorie a été créée.');
        $this->redirectRoute('admin.gallery.index');
    }
}
