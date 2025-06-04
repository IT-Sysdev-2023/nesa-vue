import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { createStyleImportPlugin, AntdResolve } from 'vite-plugin-style-import';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        createStyleImportPlugin({
            resolves: [AntdResolve()],
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
