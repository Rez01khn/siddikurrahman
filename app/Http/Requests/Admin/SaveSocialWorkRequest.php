<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSocialWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:180', 'regex:/\\A[a-z0-9]+(?:-[a-z0-9]+)*\\z/', Rule::unique('social_works', 'slug')->ignore($this->route('social_work'))],
            'short_description' => ['nullable', 'required_if:is_published,1,true', 'string', 'max:2000'],
            'full_description' => ['nullable', 'required_if:is_published,1,true', 'string', 'max:50000'],
            'activity_date' => ['nullable', 'required_if:is_published,1,true', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:today'],
            'location' => ['nullable', 'string', 'max:255'], 'image' => UpdateBiographyRequest::imageRules(), 'remove_image' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array', 'max:10'], 'images.*' => array_replace(UpdateBiographyRequest::imageRules(), [0 => 'required']),
            'gallery_alt_text' => ['nullable', 'required_with:images', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'is_featured' => ['required', 'boolean'], 'is_published' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'between:0,10000'],
            'seo_title' => ['nullable', 'string', 'max:160'], 'seo_description' => ['nullable', 'string', 'max:320']];
    }
}
