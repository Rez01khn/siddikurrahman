<?php

namespace App\Services;

use App\Models\CoachingTip;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class TrainingImageWriter
{
    public function save(Model $record, array $data, Request $request): bool
    {
        $new = null;
        try {
            if ($request->hasFile('image')) {
                $new = $request->file('image')->store('training', 'public');
                if (! $new) {
                    throw new RuntimeException('Image storage failed.');
                }
            }
            $old = DB::transaction(function () use ($record, $data, $request, $new): ?string {
                $current = $record->exists ? $record->newQuery()->lockForUpdate()->findOrFail($record->getKey()) : $record;
                $previous = $current->image;
                unset($data['image'],$data['remove_image']);
                if ($request->boolean('remove_image')) {
                    $data['image'] = null;
                }if ($new) {
                    $data['image'] = $new;
                }$current->fill($data)->save();

                return $current->image !== $previous ? $previous : null;
            });
        } catch (Throwable $e) {
            $this->cleanup($new);
            throw $e;
        }

        return $this->cleanup($old);
    }

    public function delete(Model $record): bool
    {
        $old = DB::transaction(function () use ($record): ?string {
            $current = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            $path = $current->image;
            $current->delete();

            return $path;
        });

        return $this->cleanup($old);
    }

    private function cleanup(?string $path): bool
    {
        if (! Equipment::managedCareerImage($path)) {
            return true;
        }
        try {
            if (Equipment::where('image', $path)->exists() || CoachingTip::where('image', $path)->exists()) {
                return true;
            }

            return Storage::disk('public')->delete($path);
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
