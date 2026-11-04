<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Article;
use App\Models\Biography;
use App\Models\CareerMilestone;
use App\Models\Tournament;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class CareerContentWriter
{
    public function save(Model $record, array $data, Request $request, array $imageFields, string $directory = 'career'): bool
    {
        $newPaths = [];
        try {
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($directory, 'public');
                    if (! $path) {
                        throw new RuntimeException('Image storage failed.');
                    }
                    $newPaths[$field] = $path;
                }
            }
            $oldPaths = DB::transaction(function () use ($record, $data, $request, $imageFields, $newPaths): array {
                $current = $record->exists ? $record->newQuery()->lockForUpdate()->findOrFail($record->getKey()) : $record;
                $old = [];
                foreach ($imageFields as $field) {
                    unset($data[$field], $data['remove_'.$field]);
                    $previous = $current->getAttribute($field);
                    if ($request->boolean('remove_'.$field)) {
                        $data[$field] = null;
                    }
                    if (isset($newPaths[$field])) {
                        $data[$field] = $newPaths[$field];
                    }
                    if (array_key_exists($field, $data) && $previous !== $data[$field] && $current::managedCareerImage($previous)) {
                        $old[] = $previous;
                    }
                }
                $current->fill($data)->save();

                return $old;
            });
        } catch (Throwable $exception) {
            $this->cleanup(array_values($newPaths));
            throw $exception;
        }

        return $this->cleanup($oldPaths);
    }

    public function delete(Model $record, array $imageFields): bool
    {
        $paths = DB::transaction(function () use ($record, $imageFields): array {
            $current = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            $paths = [];
            foreach ($imageFields as $field) {
                $path = $current->getAttribute($field);
                if ($current::managedCareerImage($path)) {
                    $paths[] = $path;
                }
            }
            $current->delete();

            return $paths;
        });

        return $this->cleanup($paths);
    }

    private function cleanup(array $paths): bool
    {
        $cleaned = true;
        foreach ($paths as $path) {
            try {
                $references = [Biography::class => ['profile_image', 'banner_image'], CareerMilestone::class => ['image'], Achievement::class => ['image'], Article::class => ['image'], Tournament::class => ['image'], WebsiteSetting::class => ['logo', 'favicon']];
                $managed = false;
                $shared = false;
                foreach ($references as $model => $fields) {
                    if (! $model::managedCareerImage($path)) {
                        continue;
                    }$managed = true;
                    if ($model::where(function ($query) use ($fields, $path): void {
                        foreach ($fields as $field) {
                            $query->orWhere($field, $path);
                        }
                    })->exists()) {
                        $shared = true;
                        break;
                    }
                }
                if (! $managed || $shared) {
                    continue;
                }
                if (! Storage::disk('public')->delete($path)) {
                    $cleaned = false;
                }
            } catch (Throwable $exception) {
                report($exception);
                $cleaned = false;
            }
        }

        return $cleaned;
    }
}
