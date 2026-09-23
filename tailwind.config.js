import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: 'var(--color-background)',
                surface: 'var(--color-surface)',
                'surface-secondary': 'var(--color-surface-secondary)',
                border: 'var(--color-border)',
                primary: 'var(--color-primary)',
                'primary-hover': 'var(--color-primary-hover)',
                accent: 'var(--color-accent)',
                success: 'var(--color-success)',
                warning: 'var(--color-warning)',
                danger: 'var(--color-danger)',
                text: 'var(--color-text)',
                'text-secondary': 'var(--color-text-secondary)',
                dark: 'var(--color-text)', // mapped for backward compatibility
            },
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/typography'),
    ],
};
