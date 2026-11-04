<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommunityFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'string', 'max:100'],
            'publication' => $this->routeIs('admin.*') ? ['nullable', 'in:published,draft'] : ['prohibited'],
            'featured' => ['nullable', 'in:yes,no']];
    }
}
