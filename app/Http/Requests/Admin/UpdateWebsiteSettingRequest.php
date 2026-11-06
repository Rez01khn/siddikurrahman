<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $rules = ['site_name' => ['nullable', 'string', 'max:255'], 'contact_email' => ['nullable', 'email', 'max:254'], 'contact_phone' => ['nullable', 'string', 'max:60'], 'contact_address' => ['nullable', 'string', 'max:500'], 'footer_text' => ['nullable', 'string', 'max:2000'], 'seo_title' => ['nullable', 'string', 'max:160'], 'seo_description' => ['nullable', 'string', 'max:320'], 'logo' => UpdateBiographyRequest::imageRules(), 'favicon' => ['nullable', 'image', 'mimes:png', 'extensions:png', 'max:512', 'dimensions:min_width=16,min_height=16,max_width=512,max_height=512,ratio=1/1'], 'remove_logo' => ['sometimes', 'boolean'], 'remove_favicon' => ['sometimes', 'boolean'], 'social_links' => ['nullable', 'array:facebook,instagram,x,youtube,linkedin']];
        foreach (['facebook', 'instagram', 'x', 'youtube', 'linkedin'] as $key) {
            $rules['social_links.'.$key] = ['nullable', 'string', 'max:2048', new SafeWebUrl];
        }

        return $rules;
    }
}
