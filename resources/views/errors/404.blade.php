<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Tidak Ditemukan</title>
    @vite(['resources/css/store.css'])
</head>
<body class="bg-zinc-50 min-h-screen flex items-center justify-center p-6">
<div class="text-center max-w-sm">
    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-4 mx-auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-400">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
            <path d="M12 9v4"/>
            <path d="M12 17h.01"/>
        </svg>
    </div>
    
    <h2 class="text-lg font-black text-zinc-900 mb-1">Halaman Tidak Ditemukan</h2>
    <p class="text-sm text-zinc-400 mb-6 max-w-xs mx-auto">
        {{ $exception->getMessage() ?: 'Halaman atau Toko yang Anda tuju tidak dapat ditemukan.' }}
    </p>
    
    <button onclick="window.location.href='https://pakaiapp.online'"
            class="bg-zinc-900 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-800 transition-colors active:scale-95">
        Kembali ke Pakaiapp
    </button>
</div>
</body>
</html>
