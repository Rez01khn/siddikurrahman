<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBiographyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public static function imageRules(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120',
            'dimensions:min_width=100,min_height=100,max_width=6000,max_height=6000'];
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'professional_title' => ['required', 'string', 'max:255'],
            'short_introduction' => ['nullable', 'string', 'max:2000'],
            'full_biography' => ['nullable', 'string', 'max:50000'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d', 'before:today', 'after_or_equal:1900-01-01'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'professional_start_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'profile_image' => self::imageRules(),
            'banner_image' => self::imageRules(),
            'profile_image_alt' => ['nullable', 'string', 'max:255'],
            'banner_image_alt' => ['nullable', 'string', 'max:255'],
            'remove_profile_image' => ['sometimes', 'boolean'],
            'remove_banner_image' => ['sometimes', 'boolean'],
            'additional_details' => ['nullable', 'array'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'source_note' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['required', 'boolean'],
        ];
    }
}
