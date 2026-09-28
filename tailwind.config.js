import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                paper: '#FBF7F0',
                sand: '#F1E9DA',
                brand: '#BB5318',
                ink: '#22302A',
                forest: '#1F4D3A',
                moss: '#2E6B4F',
                teal: '#2E6E6B',
                plum: '#6B4E7A',
                berry: '#8C3B4A',
                bark: '#A8552B',
                mustard: '#B98B2E',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', 'Georgia', 'serif'],
            },
        },
    },

    plugins: [forms],
};
