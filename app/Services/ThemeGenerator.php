<?php

namespace App\Services;

use InvalidArgumentException;

class ThemeGenerator
{
    private const SHADES = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];

    public function generate(string $hex): array
    {
        $rgb = $this->parseHex($hex);
        $white = [255, 255, 255];
        $dark = [17, 24, 39];
        $whiteContrast = $this->contrastRatio($rgb, $white);
        $darkContrast = $this->contrastRatio($rgb, $dark);
        $foreground = $whiteContrast > $darkContrast ? $white : $dark;
        $contrast = max($whiteContrast, $darkContrast);

        if ($contrast < 4.5) {
            throw new InvalidArgumentException('Warna utama tidak memenuhi kontras WCAG AA.');
        }

        $shades = [];
        foreach (self::SHADES as $shade) {
            $amount = match (true) {
                $shade <= 600 => (600 - $shade) / 600,
                default => ($shade - 600) / 300,
            };
            $target = $shade <= 600 ? $white : [0, 0, 0];
            $shades[$shade] = $this->toHex($this->mix($rgb, $target, $amount));
        }
        $shades[600] = $this->toHex($rgb);

        return [
            'primary' => $shades,
            'text' => $this->toHex($foreground),
            'contrast_ratio' => round($contrast, 2),
        ];
    }

    public function contrastRatio(array|string $foreground, array|string $background): float
    {
        $first = is_string($foreground) ? $this->parseHex($foreground) : $foreground;
        $second = is_string($background) ? $this->parseHex($background) : $background;
        $lighter = max($this->luminance($first), $this->luminance($second));
        $darker = min($this->luminance($first), $this->luminance($second));

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    public function parseHex(string $hex): array
    {
        if (! preg_match('/^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $hex, $matches)) {
            throw new InvalidArgumentException('Warna harus berupa hex 3 atau 6 digit.');
        }

        $value = $matches[1];
        if (strlen($value) === 3) {
            $value = implode('', array_map(fn (string $digit) => $digit.$digit, str_split($value)));
        }

        return array_map('hexdec', str_split($value, 2));
    }

    public function toRgb(string $hex): string
    {
        return implode(' ', $this->parseHex($hex));
    }

    private function luminance(array $rgb): float
    {
        $channels = array_map(function (int $channel): float {
            $value = $channel / 255;

            return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, $rgb);

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function mix(array $color, array $target, float $amount): array
    {
        return array_map(fn (int $channel, int $end) => (int) round($channel + ($end - $channel) * $amount), $color, $target);
    }

    private function toHex(array $rgb): string
    {
        return '#'.implode('', array_map(fn (int $channel) => str_pad(dechex($channel), 2, '0', STR_PAD_LEFT), $rgb));
    }
}
