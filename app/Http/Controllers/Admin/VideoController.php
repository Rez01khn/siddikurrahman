<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveVideoRequest;
use App\Models\Video;
use App\Services\GalleryWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'publication' => ['nullable', 'in:published,draft']]);
        $q = Video::query();
        if (! empty($f['q'])) {
            $q->where('title', 'like', '%'.$f['q'].'%');
        }
        if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }

        return view('admin.gallery.videos', ['title' => 'Video Gallery', 'videos' => $q->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new Video(['sort_order' => 0]));
    }

    public function edit(Video $video): View
    {
        return $this->form($video);
    }

    private function form(Video $video): View
    {
        return view('admin.gallery.video-form', ['title' => 'Video', 'video' => $video]);
    }

    public function store(SaveVideoRequest $request, GalleryWriter $writer): RedirectResponse
    {
        return $this->persist($request, new Video, $writer);
    }

    public function update(SaveVideoRequest $request, Video $video, GalleryWriter $writer): RedirectResponse
    {
        return $this->persist($request, $video, $writer);
    }

    private function persist(SaveVideoRequest $request, Video $video, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($video, $request->safe()->except(['image', 'remove_image']), $request, 'image');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'Video could not be saved. Existing data and files were kept.']);
        }

        return redirect()->route('admin.videos.index')->with('status', 'Video saved.')->with('warning', $ok ? null : 'An unused file could not be removed.');
    }

    public function destroy(Video $video, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($video);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Video could not be deleted.']);
        }

        return redirect()->route('admin.videos.index')->with('status', 'Video deleted. Shared thumbnails were preserved.')->with('warning',$ok ? null : 'An unused file could not be removed.');
    }
}
