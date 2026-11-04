<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return [
            'tournament_purse_amount' => ['nullable', 'required_with:tournament_purse_currency', 'numeric', 'between:0,99999999999999.99', 'decimal:0,2'],
            'tournament_purse_currency' => ['nullable', 'required_with:tournament_purse_amount', 'regex:/\A[A-Z]{3}\z/'],
            'name' => ['required', 'string', 'max:255'],
            'tour' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1900-01-01'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:10000'],
            'official_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'source_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'status' => ['required', Rule::in(array_keys(config('tournaments.statuses'))), Rule::when($this->boolean('is_published'), [Rule::notIn(['unconfirmed'])])],
            'is_featured' => ['required', 'boolean'],
            'is_published' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'image' => UpdateBiographyRequest::imageRules(),
            'image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
