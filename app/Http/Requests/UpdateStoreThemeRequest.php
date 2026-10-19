<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'primary' => ['required', 'string', 'regex:/^#?(?:[a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'accent' => ['required', 'string', 'regex:/^#?(?:[a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'radius' => ['required', 'in:md,lg,xl'],
        ];
    }
}
