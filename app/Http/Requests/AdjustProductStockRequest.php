<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AdjustProductStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        $store = active_store();

        return $store !== null && Gate::allows('adjustStock', $store);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'between:-4294967295,4294967295', 'not_in:0'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
