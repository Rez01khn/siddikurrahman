<?php

namespace App\Services;

use App\Models\SocialWork;
use App\Models\SocialWorkImage;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class CommunityWriter
{
    public function save(Model $record, array $data, Request $request, string $field): bool
    {
        $new = null;
        $extras = [];
        try {
            if ($request->hasFile($field)) {
                $new = $this->store($request->file($field));
            }
            if ($record instanceof SocialWork) {
                foreach ($request->file('images', []) as $file) {
                    $extras[] = $this->store($file);
                }
            }
            $old = DB::transaction(function () use ($record, $data, $request, $field, $new, $extras): ?string {
                $current = $record->exists ? $record->newQuery()->lockForUpdate()->findOrFail($record->getKey()) : $record;
                $previous = $current->getAttribute($field);
                unset($data[$field],$data['remove_'.$field],$data['images'],$data['gallery_alt_text']);
                if ($request->boolean('remove_'.$field)) {
                    $data[$field] = null;
                }
                if ($new) {
                    $data[$field] = $new;
                }
                $current->fill($data)->save();
                if ($current instanceof SocialWork) {
                    $start = ($current->images()->max('sort_order') ?? -1) + 1;
                    foreach ($extras as $i => $path) {
                        $current->images()->create(['image' => $path, 'alt_text' => $request->validated('gallery_alt_text'), 'sort_order' => min(10000, $start + $i)]);
                    }
                }

                return array_key_exists($field, $data) && $data[$field] !== $previous ? $previous : null;
            });
        } catch (Throwable $e) {
            $this->cleanup([$new, ...$extras]);
            throw $e;
        }

        return $this->cleanup([$old]);
    }

    private function store(UploadedFile $file): string
    {
        $path = $file->store('community', 'public');
        if (! $path) {
            throw new RuntimeException('Image storage failed.');
        }

        return $path;
    }

    public function delete(Model $record): bool
    {
        $paths = DB::transaction(function () use ($record): array {
            $current = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            $paths = $current instanceof Sponsor ? [$current->logo] : [$current->image];
            if ($current instanceof SocialWork) {
                $paths = [...$paths, ...$current->images()->pluck('image')->all()];
            }
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
                if (! Sponsor::managedCareerImage($path) || Sponsor::where('logo', $path)->exists() || SocialWork::where('image', $path)->exists() || SocialWorkImage::where('image', $path)->exists()) {
                    continue;
                }
                if (! Storage::disk('public')->delete($path)) {
                    $ok = false;
                }
            } catch (Throwable $e) {
                report($e);
                $ok = false;
            }
        }

        return $ok;
    }
}
