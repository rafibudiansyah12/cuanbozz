<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $groups = collect($this->input('option_groups', []))->filter(fn ($group) => is_array($group))->map(function (array $group): array {
            $group['is_required'] = filter_var($group['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN);

            return $group;
        })->all();

        $this->merge([
            'track_stock' => filter_var($this->input('track_stock', false), FILTER_VALIDATE_BOOLEAN),
            'option_groups' => $groups,
        ]);
    }

    public function authorize(): bool
    {
        $store = active_store();

        return $store !== null && Gate::allows('manageCatalog', $store);
    }

    public function rules(): array
    {
        $storeId = active_store()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->where(fn ($query) => $query->where('store_id', $storeId))],
            'price' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'unit' => ['required', 'string', 'max:30'],
            'track_stock' => ['required', 'boolean'],
            'stock' => ['nullable', 'required_if:track_stock,1', 'integer', 'min:0', 'max:4294967295'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'option_groups' => ['sometimes', 'array', 'max:20'],
            'option_groups.*.name' => ['required', 'string', 'max:100'],
            'option_groups.*.selection_type' => ['required', 'in:single,multiple'],
            'option_groups.*.is_required' => ['boolean'],
            'option_groups.*.min_select' => ['required', 'integer', 'min:0', 'max:50'],
            'option_groups.*.max_select' => ['nullable', 'integer', 'min:1', 'max:50'],
            'option_groups.*.options' => ['required', 'array', 'min:1', 'max:50'],
            'option_groups.*.options.*.name' => ['required', 'string', 'max:100'],
            'option_groups.*.options.*.price_delta' => ['required', 'integer', 'min:-2147483648', 'max:2147483647'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            foreach ($this->input('option_groups', []) as $index => $group) {
                $minimum = (int) ($group['min_select'] ?? 0);
                $maximum = isset($group['max_select']) && $group['max_select'] !== '' ? (int) $group['max_select'] : null;
                $optionCount = count($group['options'] ?? []);

                if (($group['selection_type'] ?? null) === 'single' && $maximum !== null && $maximum > 1) {
                    $validator->errors()->add("option_groups.{$index}.max_select", 'Pilihan tunggal hanya dapat memilih satu opsi.');
                }

                if (($group['is_required'] ?? false) && $minimum < 1) {
                    $validator->errors()->add("option_groups.{$index}.min_select", 'Grup wajib harus memiliki minimal satu pilihan.');
                }

                if ($maximum !== null && $minimum > $maximum) {
                    $validator->errors()->add("option_groups.{$index}.max_select", 'Batas maksimal harus sama atau lebih besar dari batas minimal.');
                }

                if ($minimum > $optionCount) {
                    $validator->errors()->add("option_groups.{$index}.min_select", 'Batas minimal tidak boleh lebih besar dari jumlah opsi.');
                }
            }
        });
    }
}
