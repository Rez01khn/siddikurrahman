<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePhotoAlbumRequest;
use App\Models\PhotoAlbum;
use App\Services\GalleryWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PhotoAlbumController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'publication' => ['nullable', 'in:published,draft']]);
        $q = PhotoAlbum::withCount('photos');
        if (! empty($f['q'])) {
            $q->where('title', 'like', '%'.$f['q'].'%');
        }
        if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }

        return view('admin.gallery.albums', ['title' => 'Photo Albums', 'albums' => $q->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new PhotoAlbum(['sort_order' => 0]));
    }

    public function edit(PhotoAlbum $photo_album): View
    {
        return $this->form($photo_album);
    }

    private function form(PhotoAlbum $album): View
    {
        return view('admin.gallery.album-form', ['title' => 'Photo Album', 'album' => $album]);
    }

    public function store(SavePhotoAlbumRequest $request, GalleryWriter $writer): RedirectResponse
    {
        return $this->persist($request, new PhotoAlbum, $writer);
    }

    public function update(SavePhotoAlbumRequest $request, PhotoAlbum $photo_album, GalleryWriter $writer): RedirectResponse
    {
        return $this->persist($request, $photo_album, $writer);
    }

    private function persist(SavePhotoAlbumRequest $request, PhotoAlbum $album, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($album, $request->safe()->except(['cover_image', 'remove_cover_image']), $request, 'cover_image');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('cover_image'))->withErrors(['save' => 'Album could not be saved. Existing content and files were kept.']);
        }

        return redirect()->route('admin.photo-albums.index')->with('status', 'Album saved.')->with('warning', $ok ? null : 'Saved, but an unused file could not be removed.');
    }

    public function destroy(PhotoAlbum $photo_album, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($photo_album);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Album could not be deleted.']);
        }

        return redirect()->route('admin.photo-albums.index')->with('status', 'Album and its photos deleted. Shared files were preserved.')->with('warning',$ok ? null : 'An unused file could not be removed.');
    }
}
