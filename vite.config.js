import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const devHost = env.VITE_DEV_HOST || 'pokemon-teams.localhost';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            host: true,
            port: Number(env.VITE_DEV_PORT || 5173),
            strictPort: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            hmr: {
                host: devHost,
            },
        },
    };
});
