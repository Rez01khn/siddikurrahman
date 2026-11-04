<?php

namespace App\Services;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GalleryWriter
{
    public function save(Model $record, array $data, Request $request, string $field): bool
    {
        $new = null;
        try {
            if ($request->hasFile($field)) {
                $new = $request->file($field)->store('gallery', 'public');
                if (! $new) {
                    throw new RuntimeException('Image storage failed.');
                }
            }
            $old = DB::transaction(function () use ($record, $data, $request, $field, $new): ?string {
                $current = $record->exists ? $record->newQuery()->lockForUpdate()->findOrFail($record->getKey()) : $record;
                $old = $current->getAttribute($field);
                unset($data[$field],$data['remove_'.$field]);
                if ($request->boolean('remove_'.$field)) {
                    $data[$field] = null;
                }
                if ($new) {
                    $data[$field] = $new;
                }
                $current->fill($data)->save();

                return array_key_exists($field, $data) && $data[$field] !== $old ? $old : null;
            });
        } catch (Throwable $exception) {
            $this->cleanup([$new]);
            throw $exception;
        }

        return $this->cleanup([$old]);
    }

    public function upload(PhotoAlbum $album, array $data, array $files): void
    {
        $paths = [];
        try {
            foreach ($files as $file) {
                $path = $file->store('gallery', 'public');
                if (! $path) {
                    throw new RuntimeException('Image storage failed.');
                }$paths[] = $path;
            }
            DB::transaction(function () use ($album, $data, $paths): void {
                $parent = PhotoAlbum::whereKey($album->id)->lockForUpdate()->firstOrFail();
                unset($data['images'],$data['image']);
                foreach ($paths as $index => $path) {
                    $parent->photos()->create([...$data, 'image' => $path, 'sort_order' => $data['sort_order'] + $index]);
                }
            });
        } catch (Throwable $exception) {
            $this->cleanup($paths);
            throw $exception;
        }
    }

    public function delete(Model $record): bool
    {
        $paths = DB::transaction(function () use ($record): array {
            $current = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            $paths = $current instanceof PhotoAlbum ? [$current->cover_image, ...$current->photos()->pluck('image')->all()] : [$current->image];
            $current->delete();

            return $paths;
        });

        return $this->cleanup($paths);
    }

    private function cleanup(array $paths): bool
    {
        $ok = true;
        foreach (array_unique(array_filter($paths)) as $path) {
            try {
                if (! Photo::managedCareerImage($path) || Photo::where('image', $path)->exists() || PhotoAlbum::where('cover_image', $path)->exists() || Video::where('image', $path)->exists()) {
                    continue;
                }
                if (! Storage::disk('public')->delete($path)) {
                    $ok = false;
                }
            } catch (Throwable $exception) {
                report($exception);
                $ok = false;
            }
        }

        return $ok;
    }
}
