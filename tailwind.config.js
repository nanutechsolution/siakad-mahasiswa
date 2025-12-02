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
                brand: {
                    blue: '#1a237e',  // Navy Blue Gelap dari Logo
                    gold: '#ffd700',  // Kuning Emas Terang dari Logo
                    dark: '#0f172a',  // Warna Gelap utk Dark Mode
                }
            }
        },
    },

    plugins: [forms],
};
