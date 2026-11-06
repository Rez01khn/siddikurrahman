<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveCareerRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $type = $this->route('type');
        abort_unless(array_key_exists($type, config('career.modules')), 404);
        $rules = [
            'description' => [$type === 'statistics' ? 'nullable' : 'required', 'string', 'max:10000'],
            'source_note' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ];
        if ($type === 'statistics') {
            return $rules + ['label' => ['required', 'string', 'max:255'], 'value' => ['required', 'string', 'max:100']];
        }

        $rules += [
            'title' => ['required', 'string', 'max:255'],
            'year' => [$type === 'achievements' ? 'required' : 'nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'event_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:today'],
            'image' => UpdateBiographyRequest::imageRules(),
            'image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
        if ($type === 'achievements') {
            $rules += [
                'category' => ['nullable', 'string', 'max:255'],
                'event_name' => ['nullable', 'string', 'max:255'],
                'is_featured' => ['nullable', 'boolean'],
            ];
        }

        return $rules;
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $validator->errors()->hasAny(['year', 'event_date']) && $this->filled('year') && $this->filled('event_date')
                && strlen((string) $this->input('event_date')) >= 4
                && $this->integer('year') !== (int) substr($this->input('event_date'), 0, 4)) {
                $validator->errors()->add('year', 'The year must match the exact date when both are supplied.');
            }
        }];
    }
}
