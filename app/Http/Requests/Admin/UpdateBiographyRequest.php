<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'additional_details' => ['nullable', 'array', 'max:12'],
            'additional_details.*' => ['array:label,value,source_note'],
            'additional_details.*.label' => ['required', 'string', 'max:100'],
            'additional_details.*.value' => ['required', 'string', 'max:255'],
            'additional_details.*.source_note' => ['required', 'string', 'max:1000'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'source_note' => ['nullable', 'required_if:is_published,1,true', 'string', 'max:2000'],
            'is_published' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_array($this->input('additional_details'))) {
            $this->merge(['additional_details' => array_values(array_filter($this->input('additional_details'),
                fn ($row) => ! is_array($row) || collect($row)->contains(fn ($value) => $value !== null && (! is_string($value) || trim($value) !== ''))))]);
        }
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $validator->errors()->hasAny(['date_of_birth', 'professional_start_year']) && $this->filled('date_of_birth') && $this->filled('professional_start_year')
                && $this->integer('professional_start_year') < (int) substr($this->input('date_of_birth'), 0, 4)) {
                $validator->errors()->add('professional_start_year', 'The professional start year cannot precede the birth year.');
            }
        }];
    }
}
