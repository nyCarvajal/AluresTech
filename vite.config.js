import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: [
            {
                find: /^tom-select$/,
                replacement: path.resolve(__dirname, 'resources/js/lib/safe-tom-select.js'),
            },
        ],
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
		'resources/scss/icons.scss',
		'resources/scss/style.scss',
		'resources/js/config.js',
            ],
            refresh: true,
        }),
    ],
});
