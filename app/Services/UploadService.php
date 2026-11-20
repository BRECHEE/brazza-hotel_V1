<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Hardened image upload handler.
 *
 * - trusts neither the client filename nor its extension
 * - checks PHP's upload error code, real MIME type and image dimensions
 * - stores only raster JPEG/PNG/WebP files under a random name
 * - writes to a directory whose .htaccess disables script execution
 */
final class UploadService
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * @param array<string, mixed> $file $_FILES entry
     * @return string public-relative path, e.g. assets/uploads/rooms/abc.jpg
     */
    public static function storeImage(array $file, string $subdirectory): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Le téléversement a échoué. Vérifiez la taille et le format du fichier.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        $maxBytes = (int) config('app.uploads.max_bytes', 4_194_304);

        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new RuntimeException('Fichier de téléversement invalide.');
        }
        if ($size <= 0 || $size > $maxBytes) {
            throw new RuntimeException('L’image ne doit pas dépasser ' . round($maxBytes / 1_048_576, 1) . ' Mo.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp);
        if (!array_key_exists($mime, self::ALLOWED)) {
            throw new RuntimeException('Format non autorisé. Utilisez JPG, PNG ou WebP.');
        }

        $info = @getimagesize($tmp);
        if ($info === false || $info[0] < 200 || $info[1] < 200) {
            throw new RuntimeException('Le fichier n’est pas une image valide ou est trop petit (200 px minimum).');
        }
        if ($info[0] > (int) config('app.uploads.max_width', 6000)
            || $info[1] > (int) config('app.uploads.max_height', 6000)) {
            throw new RuntimeException('Les dimensions de l’image sont trop grandes.');
        }

        $subdirectory = preg_replace('/[^a-z]/', '', strtolower($subdirectory)) ?? '';
        if (!in_array($subdirectory, ['rooms', 'gallery', 'settings'], true)) {
            throw new RuntimeException('Destination de téléversement invalide.');
        }

        $directory = BASE_PATH . '/public/assets/uploads/' . $subdirectory;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Le dossier de destination est inaccessible.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . self::ALLOWED[$mime];
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($tmp, $destination)) {
            throw new RuntimeException('Impossible d’enregistrer l’image téléversée.');
        }

        @chmod($destination, 0644);

        return 'assets/uploads/' . $subdirectory . '/' . $filename;
    }

    /**
     * Delete only files that live inside the uploads directory.
     */
    public static function delete(?string $publicPath): void
    {
        $publicPath = trim((string) $publicPath);
        if (!str_starts_with($publicPath, 'assets/uploads/') || str_contains($publicPath, '..')) {
            return;
        }

        $absolute = realpath(BASE_PATH . '/public/' . $publicPath);
        $uploadsRoot = realpath(BASE_PATH . '/public/assets/uploads');
        if ($absolute === false || $uploadsRoot === false || !str_starts_with($absolute, $uploadsRoot)) {
            return;
        }

        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }
}
