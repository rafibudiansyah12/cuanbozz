import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: Object.fromEntries(
                    [50, 100, 200, 300, 400, 500, 600, 700, 800, 900].map((shade) => [
                        shade,
                        `rgb(var(--primary-${shade}) / <alpha-value>)`,
                    ]),
                ),
                'on-primary': 'rgb(var(--text-on-primary) / <alpha-value>)',
                accent: 'rgb(var(--accent) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                text: 'rgb(var(--text) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                border: 'rgb(var(--border) / <alpha-value>)',
                success: 'rgb(var(--success) / <alpha-value>)',
                warning: 'rgb(var(--warning) / <alpha-value>)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
            },
            borderRadius: {
                control: 'var(--radius-control)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
