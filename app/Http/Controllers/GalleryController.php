<?php

namespace App\Http\Controllers;

use App\Models\PhotoAlbum;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    private function layout(): array
    {
        return ['sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true];
    }

    public function photos(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $q = PhotoAlbum::where('is_published', true);
        if (! empty($f['q'])) {
            $q->where('title', 'like', '%'.$f['q'].'%');
        }

        return view('gallery.albums', $this->layout() + ['albums' => $q->withCount(['photos' => fn ($photos) => $photos->where('is_published', true)])->orderBy('sort_order')->orderBy('id')->paginate(12)->withQueryString()]);
    }

    public function album(PhotoAlbum $album): View
    {
        abort_unless($album->is_published, 404);

        return view('gallery.album', $this->layout() + ['album' => $album, 'photos' => $album->photos()->where('is_published', true)->orderBy('sort_order')->orderBy('id')->paginate(24)]);
    }

    public function videos(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $q = Video::where('is_published', true);
        if (! empty($f['q'])) {
            $q->where('title', 'like', '%'.$f['q'].'%');
        }

        return view('gallery.videos', $this->layout() + ['videos' => $q->orderBy('sort_order')->orderBy('id')->paginate(12)->withQueryString()]);
    }

    public function video(Video $video): View
    {
        abort_unless($video->is_published, 404);

        return view('gallery.video', $this->layout() + ['video' => $video]);
    }
}
