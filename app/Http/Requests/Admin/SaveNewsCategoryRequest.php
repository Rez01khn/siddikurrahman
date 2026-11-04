<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveNewsCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:180', 'regex:/\\A[a-z0-9]+(?:-[a-z0-9]+)*\\z/', Rule::unique('news_categories', 'slug')->ignore($this->route('news_category'))],
            'description' => ['nullable', 'string', 'max:5000'], 'is_published' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
