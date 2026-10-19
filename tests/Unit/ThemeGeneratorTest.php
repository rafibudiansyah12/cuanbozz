<?php

use App\Services\ThemeGenerator;

it('generates all semantic primary shades while preserving the selected color', function () {
    $theme = app(ThemeGenerator::class)->generate('#10B981');

    expect($theme['primary'])->toHaveKeys([50, 100, 200, 300, 400, 500, 600, 700, 800, 900])
        ->and($theme['primary'][600])->toBe('#10b981')
        ->and($theme['text'])->toBeString()
        ->and($theme['contrast_ratio'])->toBeGreaterThanOrEqual(4.5);
});

it('chooses a foreground color that meets WCAG AA against the input color', function () {
    $generator = app(ThemeGenerator::class);
    $theme = $generator->generate('#2563EB');

    expect($generator->contrastRatio($theme['text'], '#2563EB'))->toBeGreaterThanOrEqual(4.5);
});

it('keeps every theme preset readable', function () {
    $generator = app(ThemeGenerator::class);

    $designSystem = require dirname(__DIR__, 2).'/config/design-system.php';

    foreach ($designSystem['presets'] as $preset) {
        $theme = $generator->generate($preset['primary']);

        expect($theme['contrast_ratio'])->toBeGreaterThanOrEqual(4.5);
    }
});

it('rejects a primary color when neither foreground meets WCAG AA', function () {
    expect(fn () => app(ThemeGenerator::class)->generate('#777777'))
        ->toThrow(InvalidArgumentException::class);
});

it('accepts three digit hex and rejects malformed colors', function () {
    expect(app(ThemeGenerator::class)->generate('#abc')['primary'][600])->toBe('#aabbcc');

    app(ThemeGenerator::class)->generate('not-a-color');
})->throws(InvalidArgumentException::class);
