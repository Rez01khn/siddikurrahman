<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'integer', 'exists:news_categories,id'],
            'status' => $this->routeIs('admin.*') ? ['nullable', Rule::in(['draft', 'published', 'archived'])] : ['prohibited'],
            'from' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-01'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when($this->filled('from'), ['after_or_equal:from'])]];
    }
}
