<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePhotoAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:180', 'regex:/\\A[a-z0-9]+(?:-[a-z0-9]+)*\\z/', Rule::unique('photo_albums', 'slug')->ignore($this->route('photo_album'))],
            'description' => ['nullable', 'string', 'max:10000'], 'cover_image' => UpdateBiographyRequest::imageRules(),
            'remove_cover_image' => ['sometimes', 'boolean'], 'album_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'is_featured' => ['required', 'boolean'], 'is_published' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
