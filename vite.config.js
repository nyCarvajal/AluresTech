import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    resolve: {
        alias: [
            {
                find: /^tom-select$/,
                replacement: path.resolve(__dirname, 'resources/js/lib/safe-tom-select.js'),
            },
            {
                find: /^tom-select\/dist\/js\/tom-select\.(?:complete(?:\.min)?|min)?\.js$/,
                replacement: path.resolve(__dirname, 'resources/js/lib/safe-tom-select.js'),
            },
            {
                find: /^tom-select\/dist\/js\/tom-select\.(?:base(?:\.min)?|standalone(?:\.min)?)\.js$/,
                replacement: path.resolve(__dirname, 'resources/js/lib/safe-tom-select.js'),
            },
            {
                find: /^@alures\/tom-select-source$/,
                replacement: path.resolve(__dirname, 'node_modules/tom-select/dist/js/tom-select.complete.js'),
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
