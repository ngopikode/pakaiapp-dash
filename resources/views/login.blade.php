<!DOCTYPE html>
<html lang="id" id="html-root" class="dark" data-bs-theme="dark">
<head>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Dashboard Pakaiapp | Kelola Toko & Kasir Anda</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <meta name="description"
          content="Masuk ke dashboard Pakaiapp POS untuk mengelola toko, melihat laporan penjualan realtime, dan pantau performa bisnis dari mana saja.">
    <meta name="keywords" content="login pakaiapp, dashboard kasir, masuk pos online">
    <link rel="canonical" href="https://pakaiapp.online/login">
    <!-- Fonts & Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
</head>
<body>

<!-- Nav -->
<header class="login-nav">
    <a href="/" class="logo-container">
        <span class="logo-icon">P</span>
        <span class="logo-text">Pakaiapp<span class="text-accent">.</span></span>
    </a>
    <a href="/" class="btn-back-home">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</header>

<!-- Main -->
<main class="main-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Masuk ke Toko</h1>
            <p class="login-subtitle">Masukkan alamat email pemilik toko atau subdomain toko Anda.</p>
        </div>

        <form id="formLogin">
            @csrf
            <div class="mb-4">
                <label for="login_input" class="form-label">Email Pemilik atau Subdomain</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="text" class="form-control" id="login_input"
                           placeholder="budi@email.com atau kopi-mantap" required autofocus>
                </div>
                <div class="text-start mt-2">
                    <span class="text-muted small" style="font-size: 0.75rem;">Masukkan email untuk melihat semua toko Anda, atau subdomain toko langsung.</span>
                </div>
            </div>

            <button type="submit" id="btnSubmitLogin" class="btn-accent">
                <i class="bi bi-box-arrow-in-right"></i> Temukan Toko
            </button>
        </form>

        <!-- Store list results (hidden by default) -->
        <div id="storeListContainer" class="store-list-container" style="display: none;">
            <div class="store-list-title">Toko Anda Yang Terdaftar</div>
            <div id="storeList">
                <!-- Dynamic stores will render here -->
            </div>
        </div>

        <div class="mt-4 text-center">
            <span class="text-muted small">Belum punya toko? <a href="/register"
                                                                class="text-accent text-decoration-none fw-bold">Daftar Sekarang</a></span>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="login-footer">
    &copy; 2026 Pakaiapp.online. Hak Cipta Dilindungi.
</footer>

<script src="{{ asset('js/welcome.js') }}?v={{ time() }}"></script>
</body>
</html>
