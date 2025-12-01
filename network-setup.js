import { networkInterfaces } from 'os';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// ESM __dirname replacement
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

function getNetworkIP() {
    const nets = networkInterfaces();
    const results = {};

    for (const name of Object.keys(nets)) {
        for (const net of nets[name]) {
            if (net.family === 'IPv4' && !net.internal) {
                if (!results[name]) {
                    results[name] = [];
                }
                results[name].push(net.address);
            }
        }
    }

    const wifiIP = results['Wi-Fi']?.[0] || results['wlan0']?.[0];
    const ethernetIP = results['Ethernet']?.[0] || results['eth0']?.[0];

    return wifiIP || ethernetIP || 'localhost';
}

function updateEnvFile(ip) {
    const envPath = path.join(__dirname, '.env');
    let envContent = fs.readFileSync(envPath, 'utf8');

    envContent = envContent.replace(/APP_URL=.*/g, `APP_URL=http://${ip}:8000`)
                           .replace(/NETWORK_IP=.*/g, `NETWORK_IP=${ip}`);

    fs.writeFileSync(envPath, envContent);
    console.log(`✅ Updated .env with IP: ${ip}`);
}

function updateViteConfig(ip) {
    const configPath = path.join(__dirname, 'vite.config.js');
    let configContent = fs.readFileSync(configPath, 'utf8');

    configContent = configContent.replace(/host: '[\d\.]+'/g, `host: '${ip}'`);

    fs.writeFileSync(configPath, configContent);
    console.log(`✅ Updated Vite config with IP: ${ip}`);
}

const networkIP = getNetworkIP();
console.log(`🌐 Detected network IP: ${networkIP}`);

updateEnvFile(networkIP);
updateViteConfig(networkIP);
