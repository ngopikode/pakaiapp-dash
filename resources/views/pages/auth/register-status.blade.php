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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/register-status.css') }}">
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
                    const data = res.data || {};
                    renderStatus(data.payment_status || 'failed', data.redirect_url, data.payment_url);
                    if (data.payment_status === 'pending' || data.payment_status === 'paid') {
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
