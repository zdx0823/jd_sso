const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
mix.js('resources/js/regiest.js', 'public/js').extract(['jquery', 'jquery-validation', 'validator'])
mix.js('resources/js/login.js', 'public/js').extract(['jquery', 'jquery-validation', 'validator'])
mix.js('resources/js/passwordReset.js', 'public/js').extract(['jquery', 'jquery-validation', 'validator'])
mix.js('resources/js/passwordResetForm.js', 'public/js').extract(['jquery', 'jquery-validation', 'validator'])
mix.js('resources/js/index.js', 'public/js').extract(['jquery', 'jquery-validation', 'validator'])

mix.postCss('resources/css/regiest.css', 'public/css', [
    require('tailwindcss'),
    require('autoprefixer')
])

mix.postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
    require('autoprefixer')
])
