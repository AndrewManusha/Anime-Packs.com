const mix = require('laravel-mix');

mix.babel('resources/js/app.js', 'public/js/app.js')
    .version();
   
mix .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/home.scss', 'public/css')
    .sass('resources/sass/catalog.scss', 'public/css')
    .sass('resources/sass/pack.scss', 'public/css')
    .sass('resources/sass/download.scss', 'public/css')
    .sass('resources/sass/modals/auth-modal.scss', 'public/css/modals')
    .version();

mix.webpackConfig({
    output: {
        hashFunction: "md5"
    },
    stats: {
        children: true,
    }
});

mix.options({
    terser: {
        extractComments: false,
    }
});
