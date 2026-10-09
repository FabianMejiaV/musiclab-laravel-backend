import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        // The browser runs on the host, so HMR must dial back to a published port.
        hmr: { host: 'localhost' },
        watch: {
            // Bind mounts do not deliver inotify events on Windows/macOS.
            usePolling: true,
            interval: 300,
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
