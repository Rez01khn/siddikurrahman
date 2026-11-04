<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:254'], 'subject' => ['required', 'string', 'max:200'], 'message' => ['required', 'string', 'min:10', 'max:10000'], 'website' => ['nullable', 'string', 'max:0']];
    }
}
