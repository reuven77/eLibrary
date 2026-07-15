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
                navy: {
                    DEFAULT: '#1E2A47',
                    soft: '#2A3A5C',
                },
                onionskin: {
                    DEFAULT: '#ECEAE2',
                    deep: '#E2DFD4',
                },
                brass: {
                    DEFAULT: '#B08D57',
                    soft: '#C4A574',
                },
                forest: {
                    DEFAULT: '#3F5D4E',
                    soft: '#527A66',
                },
                rust: {
                    DEFAULT: '#A6472E',
                    soft: '#C45A3D',
                },
                charcoal: {
                    DEFAULT: '#24211C',
                    muted: '#5C574E',
                },
            },
            fontFamily: {
                display: ['Newsreader', ...defaultTheme.fontFamily.serif],
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                display: ['3.5rem', { lineHeight: '1.15', letterSpacing: '-0.02em' }],
                'display-md': ['2.5rem', { lineHeight: '1.2', letterSpacing: '-0.01em' }],
                'display-sm': ['1.75rem', { lineHeight: '1.25' }],
                utility: ['0.8125rem', { lineHeight: '1.4', letterSpacing: '0.02em' }],
            },
            boxShadow: {
                card: '0 1px 0 rgba(36, 33, 28, 0.06), 0 8px 24px rgba(30, 42, 71, 0.06)',
            },
            maxWidth: {
                shelf: '72rem',
            },
        },
    },

    plugins: [forms],
};
