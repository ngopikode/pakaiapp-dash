<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/store.css'])
</head>
<body class="bg-zinc-50 min-h-screen flex items-center justify-center p-6 antialiased selection:bg-indigo-500 selection:text-white">
<div class="text-center max-w-md w-full bg-white p-10 rounded-3xl shadow-xl shadow-zinc-200/50 border border-zinc-100 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-zinc-50 rounded-full blur-3xl opacity-60"></div>

    <div class="relative z-10">
        <div class="w-20 h-20 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center justify-center mb-6 mx-auto shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                <path d="M12 9v4"/>
                <path d="M12 17h.01"/>
            </svg>
        </div>

        <h1 class="text-5xl font-black text-zinc-900 tracking-tighter mb-2">@yield('code')</h1>
        <h2 class="text-xl font-bold text-zinc-800 mb-2">@yield('title')</h2>
        <p class="text-sm text-zinc-500 mb-8 max-w-xs mx-auto leading-relaxed">
            @yield('message')
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.history.back()"
                    class="w-full sm:w-auto bg-white border border-zinc-200 text-zinc-700 px-6 py-3 rounded-xl text-sm font-bold transition-all hover:bg-zinc-50 hover:border-zinc-300 active:scale-95 focus:outline-none focus:ring-2 focus:ring-zinc-200 focus:ring-offset-2">
                Kembali
            </button>
            <a href="/"
               class="w-full sm:w-auto bg-zinc-900 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all hover:bg-zinc-800 hover:shadow-lg hover:shadow-zinc-900/20 active:scale-95 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 inline-block">
                Halaman Utama
            </a>
        </div>
    </div>
</div>
</body>
</html>
