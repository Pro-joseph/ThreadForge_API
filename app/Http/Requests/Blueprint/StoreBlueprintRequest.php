<?php

namespace App\Http\Requests\Blueprint;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlueprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    { 
        return [
        'name' => ['required', 'string', 'max:100'],
        'target_audience' => ['nullable', 'string', 'max:255'],
        'max_hashtags' => ['nullable', 'integer', 'min:0', 'max:30'],
        'tone' => ['required', 'string', 'max:100'],
        'max_characters' => ['nullable', 'integer', 'min:1', 'max:10000'],
    ];

    }
}
