<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveSocialWorkImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return ['image' => UpdateBiographyRequest::imageRules(), 'alt_text' => ['required', 'string', 'max:255'], 'caption' => ['nullable', 'string', 'max:2000'], 'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
