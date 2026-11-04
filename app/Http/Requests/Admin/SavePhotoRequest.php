<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SavePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['image' => UpdateBiographyRequest::imageRules(), 'caption' => ['nullable', 'string', 'max:2000'],
            'alt_text' => ['required', 'string', 'max:255'], 'photographer_credit' => ['nullable', 'string', 'max:255'],
            'photo_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'], 'is_featured' => ['required', 'boolean'],
            'is_published' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
