<?php

namespace App\Actions\Stores;

use App\Models\Store;
use App\Services\ThemeGenerator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class UpdateStoreTheme
{
    public function __construct(private readonly ThemeGenerator $themeGenerator) {}

    public function handle(Store $store, array $attributes): void
    {
        try {
            $this->themeGenerator->generate($attributes['primary']);
            $this->themeGenerator->parseHex($attributes['accent']);
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'primary' => 'Warna utama belum memenuhi kontras WCAG AA atau format warnanya tidak valid.',
            ]);
        }

        $store->forceFill([
            'theme' => [
                'primary' => str_starts_with($attributes['primary'], '#') ? $attributes['primary'] : '#'.$attributes['primary'],
                'accent' => str_starts_with($attributes['accent'], '#') ? $attributes['accent'] : '#'.$attributes['accent'],
                'radius' => $attributes['radius'],
            ],
        ])->save();
    }
}
