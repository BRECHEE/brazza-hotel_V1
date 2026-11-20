<?php
/**
 * Responsive image component.
 *
 * @var string $path
 * @var string $alt
 * @var string|null $class
 * @var string|null $sizes
 * @var string|null $loading
 * @var string|null $fetchpriority
 * @var int|null $width
 * @var int|null $height
 */
$path = (string) ($path ?? 'assets/images/placeholder.svg');
$alt = (string) ($alt ?? '');
$class = (string) ($class ?? '');
$sizes = (string) ($sizes ?? '(max-width: 768px) 100vw, 50vw');
$loading = (string) ($loading ?? 'lazy');
$fetchpriority = (string) ($fetchpriority ?? 'auto');
$width = $width ?? null;
$height = $height ?? null;
$sources = image_sources($path);
?>

<picture>
    <?php if ($sources['srcset'] !== ''): ?>
        <source type="image/webp" srcset="<?= e($sources['srcset']) ?>" sizes="<?= e($sizes) ?>">
    <?php endif; ?>
    <img
        src="<?= e($sources['src']) ?>"
        alt="<?= e($alt) ?>"
        <?= $class !== '' ? 'class="' . e($class) . '"' : '' ?>
        <?= $width !== null ? 'width="' . (int) $width . '"' : '' ?>
        <?= $height !== null ? 'height="' . (int) $height . '"' : '' ?>
        loading="<?= e($loading) ?>"
        decoding="async"
        fetchpriority="<?= e($fetchpriority) ?>"
    >
</picture>
