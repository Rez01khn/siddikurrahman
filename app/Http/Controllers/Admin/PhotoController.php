<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePhotoRequest;
use App\Http\Requests\Admin\UploadPhotosRequest;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Services\GalleryWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PhotoController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['album' => ['nullable', 'integer', 'exists:photo_albums,id'], 'q' => ['nullable', 'string', 'max:100'], 'publication' => ['nullable', 'in:published,draft']]);
        $q = Photo::with('album');
        if (! empty($f['album'])) {
            $q->where('photo_album_id', $f['album']);
        }
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('caption', 'like', '%'.$f['q'].'%')->orWhere('alt_text', 'like', '%'.$f['q'].'%'));
        }
        if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }

        return view('admin.gallery.photos', ['title' => 'Photo Gallery', 'photos' => $q->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(), 'albums' => PhotoAlbum::orderBy('title')->get()]);
    }

    public function create(PhotoAlbum $album): View
    {
        return view('admin.gallery.upload', ['title' => 'Upload Photos', 'album' => $album]);
    }

    public function store(UploadPhotosRequest $request, PhotoAlbum $album, GalleryWriter $writer): RedirectResponse
    {
        try {
            $writer->upload($album, $request->safe()->except('images'), $request->file('images'));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('images'))->withErrors(['save' => 'Photos could not be saved. Newly uploaded files were cleaned up.']);
        }

        return redirect()->route('admin.photos.index', ['album' => $album->id])->with('status', 'Photos uploaded. Review each caption and alt text before publishing.');
    }

    public function edit(PhotoAlbum $album, Photo $photo): View
    {
        return view('admin.gallery.photo-form', ['title' => 'Edit Photo', 'album' => $album, 'photo' => $photo]);
    }

    public function update(SavePhotoRequest $request, PhotoAlbum $album, Photo $photo, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($photo, $request->safe()->except('image'), $request, 'image');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'Photo could not be saved. Existing data and files were kept.']);
        }

        return redirect()->route('admin.photos.index', ['album' => $album->id])->with('status', 'Photo saved.')->with('warning', $ok ? null : 'An unused old file could not be removed.');
    }

    public function destroy(PhotoAlbum $album, Photo $photo, GalleryWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($photo);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Photo could not be deleted.']);
        }

        return redirect()->route('admin.photos.index', ['album' => $album->id])->with('status', 'Photo deleted. Shared files were preserved.')->with('warning', $ok ? null : 'An unused file could not be removed.');
    }
}
