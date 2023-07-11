import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';


export default defineConfig({
    //base: command === 'serve' ? '' : '/build/',
    //server: {
     //   port: 3005,
    //},
    publicDir: 'fake_dir_so_nothing_gets_copied',
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: 'resources/js/app.jsx',
        },
    },
    plugins: [
        laravel(['resources/js/app.jsx']),
        react(),
    ],
});
