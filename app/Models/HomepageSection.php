<?php

namespace App\Models;

use App\Rules\SafeHomepageLink;
use Database\Factories\HomepageSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['section_key', 'title', 'subtitle', 'description', 'image', 'button_text', 'button_url', 'additional_data', 'is_active', 'sort_order'])]
class HomepageSection extends Model
{
    /** @use HasFactory<HomepageSectionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['additional_data' => 'array', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function definition(): array
    {
        return config('homepage.'.$this->section_key, []);
    }

    public function text(string $field): string
    {
        $defaults = $this->definition()['defaults'] ?? [];
        $fallback = $defaults[$field] ?? $defaults['additional_data'][$field] ?? '';
        $value = array_key_exists($field, $defaults)
            ? $this->getAttribute($field)
            : ($this->additional_data[$field] ?? null);

        return is_string($value) && trim($value) !== '' ? $value : (string) $fallback;
    }

    public static function managedImage(?string $path): bool
    {
        return is_string($path) && preg_match('/\Ahomepage\/[a-zA-Z0-9]{40}\.(jpg|jpeg|png|webp)\z/', $path) === 1;
    }

    public function buttonUrl(array $visibleAnchors): string
    {
        $url = $this->text('button_url');
        if (! SafeHomepageLink::allows($url)) {
            return '#main';
        }
        if (str_starts_with($url, '#') && ! in_array(substr($url, 1), [...$visibleAnchors, 'main'], true)) {
            return '#main';
        }

        return $url;
    }

    public function hasImage(string $slot = 'primary'): bool
    {
        $path = $slot === 'secondary' ? ($this->additional_data['_secondary_image'] ?? null) : $this->image;

        return static::managedImage($path) && Storage::disk('public')->exists($path);
    }

    public function imageUrl(string $slot = 'primary'): ?string
    {
        if ($this->hasImage($slot)) {
            $path = $slot === 'secondary' ? $this->additional_data['_secondary_image'] : $this->image;

            return asset('storage/'.$path);
        }

        $fallback = $this->definition()[$slot === 'secondary' ? 'fallback_secondary_image' : 'fallback_image'] ?? null;

        return $fallback ? asset($fallback) : null;
    }

    public function uploadedPaths(): array
    {
        return array_values(array_filter([$this->image, $this->additional_data['_secondary_image'] ?? null],
            fn ($path) => static::managedImage($path)));
    }
}
