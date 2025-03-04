const mix = require('laravel-mix');

mix.js('resources/src/js/vendor.js', 'resources/js');
mix.copy('node_modules/laravel-echo/dist/echo.iife.js', 'resources/js/echo.js');
