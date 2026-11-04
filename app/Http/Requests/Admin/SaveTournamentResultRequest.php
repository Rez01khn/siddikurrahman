<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeWebUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTournamentResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $tournament = $this->route('tournament');
        $unique = Rule::unique('tournament_results', 'tournament_id');
        if ($this->isMethod('PUT') && $existing = $tournament->result) {
            $unique->ignore($existing->id);
        }
        $rules = [
            'tournament_id' => ['required', 'integer', Rule::in([$tournament->id]), 'exists:tournaments,id', $unique],
            'final_position' => ['nullable', 'string', 'max:30'],
            'total_score' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'score_to_par' => ['nullable', 'integer', 'between:-999,999'],
            'prize_amount' => ['nullable', 'numeric', 'regex:/\\A\\d{1,12}(?:\\.\\d{1,2})?\\z/'],
            'prize_currency' => ['nullable', 'required_with:prize_amount', 'string', 'regex:/\\A[A-Z]{3}\\z/'],
            'status' => ['required', Rule::in(array_keys(config('tournaments.result_statuses')))],
            'notes' => ['nullable', 'string', 'max:10000'],
            'source_url' => ['nullable', 'required_if:is_published,1,true', 'string', 'max:2048', new SafeWebUrl],
            'is_published' => ['required', 'boolean'],
        ];
        foreach ([1, 2, 3, 4] as $round) {
            $rules['round_'.$round] = ['nullable', 'integer', 'min:1', 'max:500'];
        }

        return $rules;
    }
}
