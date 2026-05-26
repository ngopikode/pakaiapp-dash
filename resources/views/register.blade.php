<!DOCTYPE html>
<html lang="id" id="html-root" class="dark" data-bs-theme="dark">
<head>
    <script>
        if (localStorage.getItem("theme") === "light") {
            document.documentElement.classList.remove("dark");
            document.documentElement.setAttribute("data-bs-theme", "light");
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Aplikasi Kasir POS Online Gratis | Pakaiapp</title>
    <meta name="description" content="Daftar aplikasi kasir POS pintar dari Pakaiapp. Tingkatkan omzet bisnis F&B dan Retail Anda dengan fitur pencatatan otomatis, stok, dan laporan realtime.">
    <meta name="keywords" content="daftar aplikasi kasir, kasir online, pos system, aplikasi toko, pakaiapp pos">
    <link rel="canonical" href="https://pakaiapp.online/register">
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
</head>
<body class="prompt-ui-body">

<!-- Header -->
<header class="prompt-header">
    <a href="/" class="btn-back-home">
        <i class="bi bi-arrow-left"></i> <span>Kembali ke Beranda</span>
    </a>
    <div class="prompt-logo">Pakaiapp<span class="text-accent">.</span></div>
</header>

<!-- Main Interface -->
<main class="prompt-main" id="promptMain">
    <div class="prompt-container">
        
        <!-- Center Question Area -->
        <div class="question-area" id="questionArea">
            <div class="ai-avatar">
                <i class="bi bi-stars"></i>
            </div>
            <h1 id="aiQuestion" class="ai-question">Halo! Siapa nama toko atau bisnis Anda?</h1>
            <p id="aiSubQuestion" class="ai-sub-question">Mari kita siapkan kasir cerdas Anda dalam hitungan detik.</p>
        </div>

        <!-- Dynamic Input Area -->
        <div class="interaction-area">
            
            <!-- Standard Text Input -->
            <div class="prompt-input-box" id="inputContainer">
                <input type="text" id="chatInput" class="prompt-input" placeholder="Ketik nama tokomu di sini..." autocomplete="off">
                <button id="btnSend" class="prompt-btn-send">
                    <i class="bi bi-arrow-up-short"></i>
                </button>
            </div>

            <!-- Choice Buttons (Hidden by default) -->
            <div class="prompt-choices" id="choicesContainer" style="display: none;">
                <!-- Buttons injected via JS -->
            </div>
            
            <div class="prompt-footer">
                <button type="button" id="btnStepBack" class="btn-step-back" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise"></i> Ganti Jawaban Sebelumnya
                </button>
                <div class="secure-badge">
                    <i class="bi bi-shield-check"></i> Aman & Terenkripsi
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Loading State (Full Screen) -->
<div id="loadingOverlay" class="prompt-loading-overlay" style="display: none;">
    <div class="spinner-grow text-accent" role="status"></div>
    <div id="loadingText" class="loading-text mt-3">Sedang menyiapkan toko Anda...</div>
</div>

<!-- Toast / Alerts -->
<div class="toast-container" id="customToast">
    <i class="bi bi-exclamation-circle-fill" style="color:var(--accent);"></i>
    <span id="toastMsg" style="font-weight:600; font-size:0.9rem;"></span>
</div>

<div class="custom-modal-overlay" id="customModal">
    <div class="custom-modal-box">
        <div class="modal-icon" id="modalIcon"></div>
        <h3 id="modalTitle" style="color:#fff; font-weight:700; margin-bottom:0.5rem; font-family:'Space Grotesk', sans-serif;"></h3>
        <p id="modalDesc" style="color:var(--text-muted); font-size:0.9rem; line-height:1.5;"></p>
        <button class="modal-btn" id="modalBtn" onclick="window.closeCustomAlert()">Tutup</button>
    </div>
</div>

<script src="{{ asset('js/welcome.js') }}?v={{ time() }}"></script>
</body>
</html>
