<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CoachingTip;
use App\Models\PhotoAlbum;
use App\Models\SocialWork;
use App\Models\Tournament;
use App\Models\Video;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $text = config('seo.indexable') ? "User-agent: *\nDisallow: /admin\nDisallow: /contact\nSitemap: {$base}/sitemap.xml\n" : "User-agent: *\nDisallow: /\n";

        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): StreamedResponse
    {
        return response()->stream(function (): void {
            $base = rtrim(config('app.url'), '/');
            $xml = fn (string $s): string => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach (['', '/biography', '/tournaments', '/news', '/photos', '/videos', '/sponsors', '/social-works', '/in-the-bag', '/coaching-tips'] as $path) {
                echo '<url><loc>'.$xml($base.$path).'</loc></url>';
            }
            foreach ([[Article::visible(), '/news/', 'slug'], [CoachingTip::where('is_published', true), '/coaching-tips/', 'slug'], [SocialWork::where('is_published', true), '/social-works/', 'slug'], [PhotoAlbum::where('is_published', true), '/photos/', 'slug'], [Video::where('is_published', true), '/videos/', 'slug'], [Tournament::where('is_published', true), '/tournaments/', 'id']] as [$query,$path,$key]) {
                foreach ($query->select(['id', $key, 'updated_at'])->orderBy('id')->cursor() as $record) {
                    echo '<url><loc>'.$xml($base.$path.rawurlencode((string) $record->$key)).'</loc>'.($record->updated_at ? '<lastmod>'.$record->updated_at->toAtomString().'</lastmod>' : '').'</url>';
                }
            }
            echo '</urlset>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
