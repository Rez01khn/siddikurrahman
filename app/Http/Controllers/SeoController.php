<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $text = config('seo.indexable')
            ? "User-agent: *\nDisallow: /admin\nSitemap: {$base}/sitemap.xml\n"
            : "User-agent: *\nDisallow: /\n";

        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $loc = htmlspecialchars($base, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.$loc.'</loc></url></urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
