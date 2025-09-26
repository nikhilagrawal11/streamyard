const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]);

// Copy Video.js assets
mix.copy('node_modules/video.js/dist/video-js.css', 'public/css/video-js.css');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.min.css', 'public/css/fontawesome.css');

if (mix.inProduction()) {
    mix.version();
}
