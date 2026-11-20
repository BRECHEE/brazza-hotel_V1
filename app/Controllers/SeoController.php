<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Room;

final class SeoController extends Controller
{
    public function sitemap(): never
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('rooms.index'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => route('restaurant.index'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('gallery.index'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('about.index'), 'priority' => '0.7', 'changefreq' => 'yearly'],
            ['loc' => route('contact.index'), 'priority' => '0.8', 'changefreq' => 'yearly'],
            ['loc' => route('reservation.create'), 'priority' => '0.9', 'changefreq' => 'monthly'],
        ];

        foreach (Room::allActive() as $room) {
            $urls[] = [
                'loc' => route('rooms.show', ['slug' => $room['slug']]),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ];
        }

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . e($url['loc']) . "</loc>\n";
            echo '    <lastmod>' . date('Y-m-d') . "</lastmod>\n";
            echo '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            echo '    <priority>' . $url['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
        exit;
    }

    public function robots(): never
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /reservation/merci\n";
        echo "\n";
        echo 'Sitemap: ' . route('sitemap') . "\n";
        exit;
    }
}
