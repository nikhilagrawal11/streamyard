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
    ])
    .options({
        processCssUrls: false
    });

mix.copy('node_modules/video.js/dist/video-js.css', 'public/css/video-js.css');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.min.css', 'public/css/fontawesome.css');

// Environment variables for frontend
mix.webpackConfig({
    plugins: [
        new (require('webpack')).DefinePlugin({
            'process.env': {
                MIX_PUSHER_APP_KEY: JSON.stringify(process.env.MIX_PUSHER_APP_KEY || 'local'),
                MIX_PUSHER_APP_CLUSTER: JSON.stringify(process.env.MIX_PUSHER_APP_CLUSTER || 'mt1'),
            }
        })
    ]
});

if (mix.inProduction()) {
    mix.version();
}
