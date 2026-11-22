import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { nativephpMobile, nativephpHotFile } from './vendor/nativephp/mobile/resources/js/vite-plugin.js'; 
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            hotFile: nativephpHotFile(), 
        }),
        tailwindcss(),
        nativephpMobile(),
    ],
});
