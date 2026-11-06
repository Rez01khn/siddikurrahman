<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait HasCareerImages
{
    public static function managedCareerImage(?string $path): bool
    {
        $directory = defined(static::class.'::IMAGE_DIRECTORY') ? static::IMAGE_DIRECTORY : 'career';

        return is_string($path) && preg_match('~\\A'.preg_quote($directory, '~').'/[a-zA-Z0-9]{40}\\.(jpg|jpeg|png|webp)\\z~', $path) === 1;
    }

    public function imageUrl(string $field = 'image'): ?string
    {
        $path = $this->getAttribute($field);
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (static::managedCareerImage($path) && Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return null;
    }
}
