<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AddCashierRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['role' => $this->input('role', 'cashier')]);
    }

    public function authorize(): bool
    {
        return $this->user()?->ownedStores()->exists() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)],
            'role' => ['required', 'in:admin,cashier'],
        ];
    }
}
