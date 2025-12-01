import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import { networkInterfaces } from 'os';

// Ambil IP LAN otomatis
function getLocalIP() {
    const nets = networkInterfaces();
    for (const name of Object.keys(nets)) {
        for (const net of nets[name]) {
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return '0.0.0.0';
}

const localIP = getLocalIP();
console.log("Vite running on LAN:", localIP);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',   // biar bisa diakses dari luar
        port: 5173,
        strictPort: true,
        hmr: {
            host: localIP, // HOST HMR harus IP LAN kamu
        },
    },
    define: {
        'process.env': process.env,
    },
    build: {
        commonjsOptions: {
            include: [/node_modules/],
        },
    },
    optimizeDeps: {
        include: ['lucide', 'pusher-js', 'laravel-echo'],
    },
});
