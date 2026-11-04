<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:180', 'regex:/\\A[a-z0-9]+(?:-[a-z0-9]+)*\\z/', Rule::unique('articles', 'slug')->ignore($this->route('article'))],
            'news_category_id' => ['required', 'integer', 'exists:news_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'required_if:status,published', 'string', 'max:100000'],
            'image' => UpdateBiographyRequest::imageRules(), 'remove_image' => ['sometimes', 'boolean'],
            'image_caption' => ['nullable', 'string', 'max:255'], 'author_name' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'required_if:status,published', 'date_format:Y-m-d', 'after_or_equal:1900-01-01'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])], 'is_featured' => ['required', 'boolean'],
            'tournament_id' => ['nullable', 'integer', 'exists:tournaments,id'],
            'source_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'seo_title' => ['nullable', 'string', 'max:160'], 'seo_description' => ['nullable', 'string', 'max:320'],
            'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
