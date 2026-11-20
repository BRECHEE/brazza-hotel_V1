"""Generate responsive WebP derivatives for the illustrative source images.

Usage (development machine with Pillow installed):
    python bin/optimize-images.py

Source JPEGs are kept for Open Graph compatibility and as <img> fallbacks.
The generated files live in public/assets/images/optimized/.
"""

from __future__ import annotations

from pathlib import Path

from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "public" / "assets" / "images"
TARGET = SOURCE / "optimized"
WIDTHS = (480, 960, 1600)
QUALITY = 82


def main() -> None:
    TARGET.mkdir(parents=True, exist_ok=True)
    for source in sorted(SOURCE.glob("*.jpg")):
        with Image.open(source) as original:
            original = ImageOps.exif_transpose(original).convert("RGB")
            for width in WIDTHS:
                if original.width < width:
                    continue
                height = round(original.height * width / original.width)
                derived = original.resize((width, height), Image.Resampling.LANCZOS)
                output = TARGET / f"{source.stem}-{width}.webp"
                derived.save(output, "WEBP", quality=QUALITY, method=6)
                print(f"{output.relative_to(ROOT)}  {output.stat().st_size // 1024} KB")

    hero = SOURCE / "hero.jpg"
    if hero.exists():
        with Image.open(hero) as image:
            image = ImageOps.exif_transpose(image).convert("RGB")
            og = ImageOps.fit(image, (1200, 630), Image.Resampling.LANCZOS)
            og.save(SOURCE / "og-brazza-hotel.jpg", "JPEG", quality=86, optimize=True, progressive=True)
            print("public/assets/images/og-brazza-hotel.jpg")


if __name__ == "__main__":
    main()
