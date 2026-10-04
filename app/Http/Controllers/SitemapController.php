<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Statamic\Facades\Entry;

class SitemapController extends Controller
{
    /** Collections whose published entries are listed, minus any marked "Hide from Search Engines". */
    private const COLLECTIONS = ['pages', 'blog', 'use_cases'];

    /** Routes defined in routes/web.php rather than as entries. */
    private const EXTRA_PATHS = ['/blog'];

    public function __invoke(): Response
    {
        $urls = Entry::query()
            ->whereIn('collection', self::COLLECTIONS)
            ->whereStatus('published')
            ->get()
            ->reject(fn ($entry) => $entry->get('seo_noindex') || ! $entry->url())
            ->map(fn ($entry) => ['loc' => $entry->absoluteUrl(), 'lastmod' => $entry->lastModified()])
            ->concat(collect(self::EXTRA_PATHS)->map(fn ($path) => ['loc' => url($path), 'lastmod' => null]))
            ->unique('loc')
            ->sortBy('loc');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.e($url['loc']).'</loc>'
                .($url['lastmod'] ? '<lastmod>'.$url['lastmod']->toAtomString().'</lastmod>' : '')
                ."</url>\n";
        }

        return response($xml.'</urlset>'."\n", 200, ['Content-Type' => 'application/xml']);
    }
}
