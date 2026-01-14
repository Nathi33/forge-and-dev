import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    root: path.resolve(__dirname, 'assets'), // dossier des assets
    build: {
        outDir: path.resolve(__dirname, 'public/build'), // dossier où seront copiés les fichiers compilés
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'assets/app.js')
            }
        }
    }
});
