<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CheckoutCashRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $serviceType = $this->input('service_type', 'take_away');
        $this->merge([
            'service_type' => $serviceType,
            'table_number' => $serviceType === 'dine_in' ? $this->input('table_number') : null,
        ]);
    }

    public function authorize(): bool
    {
        $store = active_store();

        return $store !== null && Gate::allows('view', $store);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'integer', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'items.*.note' => ['nullable', 'string', 'max:500'],
            'items.*.options' => ['sometimes', 'array', 'max:100'],
            'items.*.options.*' => ['required', 'integer', 'distinct:strict', 'min:1'],
            'cash_received' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'service_type' => ['required', 'in:dine_in,take_away'],
            'table_number' => ['nullable', 'required_if:service_type,dine_in', 'string', 'max:20'],
        ];
    }
}
