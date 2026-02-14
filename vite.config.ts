import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vite';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    root: fileURLToPath(new URL('./resources/app', import.meta.url)),
    plugins: [
        vue(),
    ],
    build: {
        outDir: fileURLToPath(new URL('./public/dist', import.meta.url)),
        manifest: 'manifest.json',
        emptyOutDir: true,
        rollupOptions: {
            input: fileURLToPath(new URL('./resources/app/main.ts', import.meta.url))
        }
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/app', import.meta.url))
        }
    }
});