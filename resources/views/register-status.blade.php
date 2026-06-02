<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran - Pakaiapp POS</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts & Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg: #0D1117;
            --surface: #1C2333;
            --surface-hover: #262F44;
            --accent: #F97316;
            --accent-hover: #EA580C;
            --text: #F3F4F6;
            --text-muted: #9CA3AF;
            --border: #2D3748;
            --border-hover: #4A5568;
            --danger: #EF4444;
            --success: #22C55E;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Navbar */
        .status-nav {
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background-color: var(--bg);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text);
        }

        .logo-icon {
            background-color: var(--accent);
            color: #fff;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .text-accent {
            color: var(--accent) !important;
        }

        /* Container & Card */
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }

        .status-card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            width: 100%;
            max-width: 580px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }

        .badge-pending {
            background-color: rgba(249, 115, 22, 0.1);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .badge-paid {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badge-created {
            background-color: rgba(34, 197, 94, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badge-failed {
            background-color: rgba(239, 68, 110, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .status-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--text);
            margin-bottom: 0.75rem;
        }

        .status-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 2.5rem;
        }

        /* Detail List */
        .detail-box {
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: left;
            margin-bottom: 2.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-row:first-child {
            padding-top: 0;
        }

        .detail-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .detail-value {
            font-size: 0.95rem;
            color: var(--text);
            font-weight: 700;
        }

        /* Onboarding Steps */
        .onboarding-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
        }

        .onboarding-steps::before {
            content: '';
            position: absolute;
            top: 1.25rem;
            left: 10%;
            right: 10%;
            height: 2px;
            background-color: var(--border);
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 30%;
        }

        .step-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: var(--bg);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 700;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .step-item.active .step-icon {
            border-color: var(--accent);
            color: var(--accent);
            background-color: var(--bg);
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.2);
        }

        .step-item.completed .step-icon {
            border-color: var(--success);
            color: #fff;
            background-color: var(--success);
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step-item.active .step-label {
            color: var(--accent);
        }

        .step-item.completed .step-label {
            color: var(--success);
        }

        /* Loader */
        .spinner-custom {
            width: 4rem;
            height: 4rem;
            border: 4px solid var(--border);
            border-top: 4px solid var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Success Animation */
        .success-checkmark {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background-color: rgba(34, 197, 94, 0.1);
            border: 2px solid var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--success);
            margin: 0 auto 2rem auto;
            animation: scaleIn 0.3s ease-out;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0.7);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Buttons */
        .btn-accent {
            background-color: var(--accent);
            border: none;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            transition: background-color 0.2s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
        }

        .btn-accent:hover {
            background-color: var(--accent-hover);
            color: #fff;
        }

        .btn-outline-custom {
            background-color: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.85rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }

        .btn-outline-custom:hover {
            border-color: var(--border-hover);
            background-color: rgba(255, 255, 255, 0.02);
            color: var(--text);
        }

        /* Footer */
        .status-footer {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

<!-- Nav -->
<header class="status-nav">
    <div class="logo-container">
        <span class="logo-icon">P</span>
        <span class="logo-text">Pakaiapp<span class="text-accent">.</span></span>
    </div>
</header>

<!-- Main -->
<main class="main-container">
    <div class="status-card">

        <!-- DYNAMIC CONTENT STARTS HERE -->
        <div id="statusContent">
            <!-- Fallback Loading while JS queries -->
            <div class="spinner-custom"></div>
            <h2 class="status-title">Memuat Status...</h2>
            <p class="status-desc">Harap tunggu sementara sistem memeriksa status pendaftaran Anda.</p>
        </div>
        <!-- DYNAMIC CONTENT ENDS HERE -->

        <!-- Detail Box (Always visible) -->
        <div class="detail-box">
            <div class="detail-row">
                <span class="detail-label">Invoice Pendaftaran</span>
                <span class="detail-value" style="font-family: monospace;">{{ $registration->invoice_code }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nama Toko</span>
                <span class="detail-value">{{ $registration->store_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pemilik</span>
                <span class="detail-value">{{ $registration->owner_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Paket Dipilih</span>
                <span class="detail-value text-uppercase text-accent">{{ $registration->plan }}</span>
            </div>
            @if($registration->amount > 0)
                <div class="detail-row">
                    <span class="detail-label">Total Pembayaran</span>
                    <span
                        class="detail-value text-accent">Rp {{ number_format($registration->amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

    </div>
</main>

<!-- Footer -->
<footer class="status-footer">
    &copy; 2026 Pakaiapp.online. Seluruh sistem aman dilindungi enkripsi SSL.
</footer>

<!-- Custom Script for Real-Time Polling -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoiceCode = "{{ $registration->invoice_code }}";

        function fetchRegistrationDetails() {
            fetch('/api/register/status/' + invoiceCode)
                .then(r => r.json())
                .then(res => {
                    renderStatus(res.status, res.redirect_url, res.payment_url);
                    if (res.status === 'pending' || res.status === 'paid') {
                        setTimeout(fetchRegistrationDetails, 3000);
                    }
                })
                .catch(() => {
                    // Fallback polling in case of transient errors
                    setTimeout(fetchRegistrationDetails, 5000);
                });
        }

        function renderStatus(status, redirectUrl, paymentUrl) {
            const container = document.getElementById('statusContent');

            if (status === 'pending') {
                container.innerHTML = `
                        <div class="status-badge badge-pending"><i class="bi bi-clock"></i> Menunggu Pembayaran</div>
                        <h2 class="status-title">Selesaikan Pembayaran</h2>
                        <p class="status-desc">Kami mendeteksi tagihan pembayaran Anda belum terbayar. Silakan klik tombol di bawah untuk menyelesaikan pembayaran Anda.</p>
                        ${paymentUrl ? `<a href="${paymentUrl}" target="_blank" class="btn-accent"><i class="bi bi-wallet2"></i> Bayar Sekarang</a>` : ''}
                        <div class="onboarding-steps mt-5">
                            <div class="step-item active"><div class="step-icon">1</div><div class="step-label">Bayar</div></div>
                            <div class="step-item"><div class="step-icon">2</div><div class="step-label">Proses</div></div>
                            <div class="step-item"><div class="step-icon">3</div><div class="step-label">Selesai</div></div>
                        </div>
                    `;
            } else if (status === 'paid') {
                container.innerHTML = `
                        <div class="spinner-custom"></div>
                        <div class="status-badge badge-paid"><i class="bi bi-check-circle"></i> Pembayaran Diterima</div>
                        <h2 class="status-title">Menyiapkan Toko Anda...</h2>
                        <p class="status-desc">Terima kasih atas pembayaran Anda! Sistem kami sedang memproses dan mengkonfigurasi database toko, subdomain, dan credentials kasir Anda secara otomatis. Proses ini biasanya memakan waktu kurang dari 30 detik.</p>
                        <div class="onboarding-steps mt-5">
                            <div class="step-item completed"><div class="step-icon"><i class="bi bi-check"></i></div><div class="step-label">Bayar</div></div>
                            <div class="step-item active"><div class="step-icon">2</div><div class="step-label">Proses</div></div>
                            <div class="step-item"><div class="step-icon">3</div><div class="step-label">Selesai</div></div>
                        </div>
                    `;
            } else if (status === 'created') {
                container.innerHTML = `
                        <div class="success-checkmark"><i class="bi bi-check-lg"></i></div>
                        <div class="status-badge badge-created"><i class="bi bi-check-circle"></i> Toko Aktif</div>
                        <h2 class="status-title">Toko Anda Siap Digunakan!</h2>
                        <p class="status-desc">Selamat! Toko cloud Pintar Anda telah berhasil dibuat dan dikonfigurasi di server Pakaiapp. Email selamat datang beserta rincian akses login telah dikirimkan ke kotak masuk Anda.</p>
                        <a href="${redirectUrl}" class="btn-accent"><i class="bi bi-box-arrow-in-right"></i> Buka Dashboard Toko</a>
                        <div class="onboarding-steps mt-5">
                            <div class="step-item completed"><div class="step-icon"><i class="bi bi-check"></i></div><div class="step-label">Bayar</div></div>
                            <div class="step-item completed"><div class="step-icon"><i class="bi bi-check"></i></div><div class="step-label">Proses</div></div>
                            <div class="step-item completed"><div class="step-icon"><i class="bi bi-check"></i></div><div class="step-label">Selesai</div></div>
                        </div>
                    `;
            } else if (status === 'failed') {
                container.innerHTML = `
                        <div class="status-badge badge-failed"><i class="bi bi-x-circle"></i> Pendaftaran Gagal</div>
                        <h2 class="status-title">Pendaftaran Gagal</h2>
                        <p class="status-desc">Terjadi kesalahan sistem saat menyiapkan toko Anda atau pembayaran Anda ditolak. Silakan hubungi support kami via WhatsApp untuk penyelesaian manual.</p>
                        <a href="https://wa.me/6285172441544" target="_blank" class="btn-accent" style="background-color:#22c55e;"><i class="bi bi-whatsapp"></i> Chat Support Admin</a>
                        <a href="/register" class="btn-outline-custom">Daftar Ulang</a>
                    `;
            }
        }

        // Start fetching status!
        fetchRegistrationDetails();
    });
</script>
</body>
</html>
