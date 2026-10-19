<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class RejectApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDeveloper() ?? false;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:2000']];
    }
}
