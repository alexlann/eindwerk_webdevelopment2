const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        fontFamily: {
            sans: ['Yantramanav', ...defaultTheme.fontFamily.sans],
            serif: ['Orelega One', ...defaultTheme.fontFamily.serif],
        },
        extend: {
            colors: {
                'red': '#F28482',
                'green': '#84A59D',
                'pink': '#F5CAC3',
                'rose': '#F7EDE2',
                'gray': '#D4D4D4',
                'black': '#151515',
                'gray-dark': '#545454',
                'gray-light': '#F4F4F4',
            },
        },

    },

    plugins: [require('@tailwindcss/forms')],
};
