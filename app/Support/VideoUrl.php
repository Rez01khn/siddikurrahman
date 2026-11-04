<?php

namespace App\Support;

use App\Rules\SafeWebUrl;

class VideoUrl
{
    public static function embed(string $url): ?string
    {
        if (! SafeWebUrl::allows($url)) {
            return null;
        }
        $parts = parse_url($url);
        if (strtolower($parts['scheme']) !== 'https') {
            return null;
        }
        $host = strtolower($parts['host']);
        $path = trim($parts['path'] ?? '', '/');
        $id = null;
        if ($host === 'youtu.be') {
            $id = $path;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (preg_match('~^(?:shorts|embed)/([a-zA-Z0-9_-]{11})$~', $path, $match)) {
                $id = $match[1];
            }
        }
        if (is_string($id) && preg_match('/\\A[a-zA-Z0-9_-]{11}\\z/', $id)) {
            return 'https://www.youtube-nocookie.com/embed/'.$id;
        }
        if (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true) && preg_match('~^(?:video/)?([0-9]{1,12})$~', $path, $match)) {
            return 'https://player.vimeo.com/video/'.$match[1];
        }

        return null;
    }
}
