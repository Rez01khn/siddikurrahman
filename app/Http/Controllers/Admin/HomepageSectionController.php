<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomepageSectionRequest;
use App\Models\HomepageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class HomepageSectionController extends Controller
{
    public function index(): View
    {
        return view('admin.homepage.index', [
            'title' => 'Homepage Management',
            'sections' => HomepageSection::whereIn('section_key', array_keys(config('homepage')))
                ->orderBy('sort_order')->orderBy('section_key')->get(),
            'definitions' => config('homepage'),
        ]);
    }

    public function create(): View
    {
        return view('admin.homepage.create', [
            'title' => 'Add Homepage Section',
            'available' => collect(config('homepage'))->except(HomepageSection::pluck('section_key')->all()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_key' => ['required', Rule::in(array_keys(config('homepage'))), 'unique:homepage_sections,section_key'],
        ]);
        HomepageSection::create(config('homepage.'.$data['section_key'].'.defaults'));

        return redirect()->route('admin.homepage.index')->with('status', 'Homepage section added with the original default content.');
    }

    public function edit(HomepageSection $homepageSection): View
    {
        abort_unless(array_key_exists($homepageSection->section_key, config('homepage')), 404);

        return view('admin.homepage.edit', [
            'title' => 'Edit '.$homepageSection->definition()['label'],
            'section' => $homepageSection,
        ]);
    }

    public function update(UpdateHomepageSectionRequest $request, HomepageSection $homepageSection): RedirectResponse
    {
        abort_unless(array_key_exists($homepageSection->section_key, config('homepage')), 404);
        $newPaths = [];
        try {
            foreach (['image', 'secondary_image'] as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('homepage', 'public');
                    if (! $path) {
                        throw new \RuntimeException('Image storage failed.');
                    }
                    $newPaths[$field] = $path;
                }
            }

            $oldPaths = DB::transaction(function () use ($request, $homepageSection, $newPaths): array {
                $section = HomepageSection::whereKey($homepageSection->id)->lockForUpdate()->firstOrFail();
                $oldPaths = $section->uploadedPaths();
                $data = $request->safe()->only(['title', 'subtitle', 'description', 'button_text', 'button_url', 'is_active', 'sort_order']);
                $additional = array_replace($section->additional_data ?? [], $request->validated('additional_data', []));
                if ($request->boolean('remove_image')) {
                    $data['image'] = null;
                }
                if ($request->boolean('remove_secondary_image')) {
                    unset($additional['_secondary_image']);
                }
                if (isset($newPaths['image'])) {
                    $data['image'] = $newPaths['image'];
                }
                if (isset($newPaths['secondary_image'])) {
                    $additional['_secondary_image'] = $newPaths['secondary_image'];
                }
                $data['additional_data'] = $additional;
                $section->update($data);

                return array_diff($oldPaths, $section->uploadedPaths());
            });
        } catch (Throwable $exception) {
            $this->removeFiles(array_values($newPaths));
            report($exception);

            return back()->withInput($request->except(['image', 'secondary_image']))
                ->withErrors(['save' => 'The section could not be saved. Your existing content and images were kept. Please try again.']);
        }

        $cleaned = $this->removeFiles($oldPaths);

        return redirect()->route('admin.homepage.edit', $homepageSection)
            ->with('status', 'Homepage section saved. Changes are live.')
            ->with('warning', $cleaned ? null : 'Content saved, but an unused old image could not be removed. Please contact the site maintainer.');
    }

    public function toggle(HomepageSection $homepageSection): RedirectResponse
    {
        abort_unless(array_key_exists($homepageSection->section_key, config('homepage')), 404);
        DB::transaction(function () use ($homepageSection): void {
            $section = HomepageSection::whereKey($homepageSection->id)->lockForUpdate()->firstOrFail();
            $section->update(['is_active' => ! $section->is_active]);
        });

        return back()->with('status', 'Section visibility updated.');
    }

    public function destroy(HomepageSection $homepageSection): RedirectResponse
    {
        abort_unless(array_key_exists($homepageSection->section_key, config('homepage')), 404);
        $paths = DB::transaction(function () use ($homepageSection): array {
            $section = HomepageSection::whereKey($homepageSection->id)->lockForUpdate()->firstOrFail();
            $paths = $section->uploadedPaths();
            $section->delete();

            return $paths;
        });
        $cleaned = $this->removeFiles($paths);

        return redirect()->route('admin.homepage.index')
            ->with('status', 'Customization deleted. The public section now uses its original fallback content. Add it again to resume editing.')
            ->with('warning', $cleaned ? null : 'An unused image could not be removed. Please contact the site maintainer.');
    }

    private function removeFiles(array $paths): bool
    {
        $success = true;
        foreach ($paths as $path) {
            if (! HomepageSection::managedImage($path)) {
                continue;
            }
            try {
                if (! Storage::disk('public')->delete($path)) {
                    $success = false;
                }
            } catch (Throwable $exception) {
                report($exception);
                $success = false;
            }
        }
        if (! $success) {
            Log::warning('Homepage image cleanup did not finish.');
        }

        return $success;
    }
}
