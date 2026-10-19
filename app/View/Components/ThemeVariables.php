<?php

namespace App\View\Components;

use App\Services\ThemeGenerator;
use Illuminate\View\Component;
use Illuminate\View\View;

class ThemeVariables extends Component
{
    public string $declarations;

    public function __construct(?string $primary = null, ?string $accent = null, ?string $radius = null, ?ThemeGenerator $generator = null)
    {
        $generator ??= app(ThemeGenerator::class);
        $primary ??= config('design-system.default_primary');
        $accent ??= config('design-system.tokens.accent');
        $radius ??= config('design-system.default_radius');
        $theme = $generator->generate($primary);
        $variables = [];

        foreach ($theme['primary'] as $shade => $hex) {
            $variables[] = "--primary-{$shade}: ".$generator->toRgb($hex).';';
        }
        $variables[] = '--text-on-primary: '.$generator->toRgb($theme['text']).';';
        $variables[] = '--accent: '.$generator->toRgb($accent).';';
        $variables[] = '--radius-control: '.match ($radius) {
            'md' => '0.375rem',
            'xl' => '0.75rem',
            default => '0.5rem',
        }.';';

        foreach (config('design-system.tokens') as $name => $hex) {
            if ($name === 'accent') {
                continue;
            }

            $variables[] = "--{$name}: ".$generator->toRgb($hex).';';
        }

        $this->declarations = implode(' ', $variables);
    }

    public function render(): View
    {
        return view('components.theme-variables');
    }
}
