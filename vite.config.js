import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Pastikan manifest.json selalu di-generate untuk @vite() di production.
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
    },
});
