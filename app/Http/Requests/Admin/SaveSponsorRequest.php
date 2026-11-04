<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSponsorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $logoRules = UpdateBiographyRequest::imageRules();
        $logoRules[count($logoRules) - 1] = 'dimensions:min_width=16,min_height=16,max_width=6000,max_height=6000';

        return ['name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:180', 'regex:/\\A[a-z0-9]+(?:-[a-z0-9]+)*\\z/', Rule::unique('sponsors', 'slug')->ignore($this->route('sponsor'))],
            'logo' => $logoRules, 'remove_logo' => ['sometimes', 'boolean'], 'website_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'source_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl], 'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:10000'], 'partnership_start_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-01'],
            'partnership_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', Rule::when($this->filled('partnership_start_date'), ['after_or_equal:partnership_start_date'])],
            'is_featured' => ['required', 'boolean'], 'is_published' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
