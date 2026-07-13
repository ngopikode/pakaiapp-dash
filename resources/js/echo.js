import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Ambil konfigurasi Reverb dari meta tags yang di-inject oleh Blade
const reverbKey = document.querySelector('meta[name="reverb-app-key"]')?.getAttribute('content') || import.meta.env.VITE_REVERB_APP_KEY;
const reverbPort = document.querySelector('meta[name="reverb-port"]')?.getAttribute('content');
const reverbScheme = document.querySelector('meta[name="reverb-scheme"]')?.getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: reverbKey,
    wsHost: window.location.hostname, // Tetap dinamis agar support tenant/multi-domain
    wsPort: reverbPort ? parseInt(reverbPort) : 80,
    wssPort: reverbPort ? parseInt(reverbPort) : 443,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
});
