<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeHomepageLink;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $section = $this->route('homepageSection');
        abort_unless(array_key_exists($section->section_key, config('homepage')), 404);
        $definition = $section->definition();
        $keys = array_keys($definition['defaults']['additional_data']);
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=100,min_height=100,max_width=6000,max_height=6000'];
        $rules = [
            'title' => ['required', 'string', 'max:500'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'button_text' => ['nullable', 'string', 'max:160'],
            'button_url' => ['nullable', 'string', 'max:255', new SafeHomepageLink],
            'additional_data' => ['sometimes', 'array:'.implode(',', $keys)],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'image' => $definition['image_enabled'] ? $imageRules : ['prohibited'],
            'secondary_image' => $definition['secondary_image_enabled'] ? $imageRules : ['prohibited'],
            'remove_image' => ['sometimes', 'boolean'],
            'remove_secondary_image' => ['sometimes', 'boolean'],
        ];
        foreach ($keys as $key) {
            $rules['additional_data.'.$key] = ['nullable', 'string', 'max:5000'];
        }

        return $rules;
    }
}
