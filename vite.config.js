import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    root: path.resolve(__dirname, 'assets'),
    build: {
        outDir: path.resolve(__dirname, 'public/build'),
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'assets/app.js')
            },
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && /\.(png|jpe?g|gif|webp|svg)$/.test(assetInfo.name)) {
                        // garder le nom original et le placer dans images/
                        return 'images/[name][extname]';
                    }
                    // JS et CSS hashés comme d’habitude
                    return '[name].[hash][extname]';
                }
            }
        }
    }
});