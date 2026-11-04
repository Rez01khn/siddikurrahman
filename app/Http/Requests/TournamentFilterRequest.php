<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(config('tournaments.statuses')))],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 20)],
            'publication' => $this->routeIs('admin.*') ? ['nullable', Rule::in(['published', 'draft'])] : ['prohibited'],
            'result_status' => ['nullable', Rule::in(array_keys(config('tournaments.result_statuses')))],
        ];
    }
}
