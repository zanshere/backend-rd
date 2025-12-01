import { spawn } from 'child_process';
import { networkInterfaces } from 'os';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// ESM __dirname replacement
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

function getNetworkIP() {
    const nets = networkInterfaces();
    for (const name of Object.keys(nets)) {
        for (const net of nets[name]) {
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return 'localhost';
}

function showNetworkInfo(ip) {
    console.log('\n=========================================');
    console.log('🚀 Laravel + Vite Development Server');
    console.log('=========================================');
    console.log(`📡 Network IP: ${ip}`);
    console.log(`🌐 Laravel URL: http://${ip}:8000`);
    console.log(`⚡ Vite Dev Server: http://${ip}:5173`);
    console.log('=========================================');
    console.log('📱 Access from other devices:');
    console.log(`   - Laravel: http://${ip}:8000`);
    console.log(`   - Vite Assets: http://${ip}:5173`);
    console.log('=========================================\n');
}

function updateEnvFile(ip) {
    const envPath = path.join(__dirname, '.env');
    if (fs.existsSync(envPath)) {
        let envContent = fs.readFileSync(envPath, 'utf8');
        envContent = envContent.replace(/APP_URL=.*/g, `APP_URL=http://${ip}:8000`);
        fs.writeFileSync(envPath, envContent);
    }
}

const networkIP = getNetworkIP();
updateEnvFile(networkIP);
showNetworkInfo(networkIP);

// Start Laravel
console.log('🚀 Starting Laravel server...');
const laravel = spawn('php', ['artisan', 'serve', '--host=0.0.0.0', '--port=8000'], {
    stdio: 'inherit',
    shell: true
});

// Start Vite
console.log('⚡ Starting Vite dev server...');
const vite = spawn('npm', ['run', 'dev'], {
    stdio: 'inherit',
    shell: true,
    env: {
        ...process.env,
        HOST: networkIP
    }
});

// Exit handler
process.on('SIGINT', () => {
    console.log('\n🛑 Shutting down servers...');
    laravel.kill();
    vite.kill();
    process.exit(0);
});
