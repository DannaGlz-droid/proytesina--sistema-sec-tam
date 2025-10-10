import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js', // ← Añade esta línea
    ],

    theme: {
        extend: {
            fontFamily: {
                //sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                sans: ['Open Sans', 'sans-serif'],
                montserrat: ['Montserrat', 'sans-serif'], // ← Nueva fuente
                lora: ['Lora', 'serif'], // ← Nueva fuente
            },
            colors: {
                'header': '#611132',
                'nav': '#3A0B1F',
                'color-text': '#404041',
                'color-text-alt': '#DB9703',
                'halt': '#7E3D58',
            },

            fontSize: {
        '14': '14px',
        '15': '15px',
        '16': '16px',
        '17': '17px',
        '18': '18px',
        '19': '19px',
      },
        },
    },

    plugins: [forms],
};
