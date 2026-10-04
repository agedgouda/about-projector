import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/site.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            // Editors and tools often save by replacing the file, which native watching misses on macOS,
            // so new Tailwind classes in templates never reached the dev CSS. Poll instead, limited to source files.
            usePolling: true,
            interval: 300,
            ignored: ['**/storage/**', '**/vendor/**', '**/public/**', '**/.claude/**', '**/database/**', '**/node_modules/**'],
        },
    },
});
