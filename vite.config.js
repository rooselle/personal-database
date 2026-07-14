import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/passkeys.js',
            ],
            refresh: true,
            fonts: [
                bunny('Bitter', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Karla', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Caveat', {
                    weights: [500, 600, 700],
                }),
                bunny('Playwrite FR Moderne', {
                    weights: [400],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
