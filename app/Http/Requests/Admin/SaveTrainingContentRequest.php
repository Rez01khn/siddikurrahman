<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTrainingContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $model = config('training.'.$this->route('type').'.model');
        abort_unless($model, 404);
        $table = (new $model)->getTable();

        return ['title' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:180', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', Rule::unique($table, 'slug')->ignore($this->route('record'))],
            'category' => ['nullable', 'string', 'max:100'], 'excerpt' => ['nullable', 'string', 'max:2000'], 'description' => ['nullable', 'required_if:is_published,1,true', 'string', 'max:50000'], 'image' => UpdateBiographyRequest::imageRules(), 'image_alt' => ['nullable', 'required_with:image', 'string', 'max:255'], 'remove_image' => ['sometimes', 'boolean'], 'source_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'brand' => $this->route('type') === 'equipment' ? ['nullable', 'string', 'max:100'] : ['prohibited'], 'model_name' => $this->route('type') === 'equipment' ? ['nullable', 'string', 'max:255'] : ['prohibited'],
            'seo_title' => ['nullable', 'string', 'max:160'], 'seo_description' => ['nullable', 'string', 'max:320'], 'is_published' => ['required', 'boolean'], 'is_featured' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'between:0,10000']];
    }
}
