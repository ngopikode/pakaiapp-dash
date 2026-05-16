import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/store.css',
                'resources/js/store.js'
            ],
            refresh: true
        })
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**']
        }
    }
});
