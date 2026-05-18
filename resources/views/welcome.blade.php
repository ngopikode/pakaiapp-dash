<!DOCTYPE html>
<html lang="id" data-bs-theme="light" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pakaiapp.online - Kasir SaaS Masa Depan</title>

    <!-- External Libraries for OP Interactivity -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite Assets (Pastikan app.scss kamu sudah update sesuai yang sebelumnya) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* Custom Glassmorphism & Animations */
        .glass-card {
            background: rgba(var(--bs-body-bg-rgb), 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
        }

        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: var(--bs-primary);
            border-radius: 50%;
            margin: 0 2px;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }

        /* Range Slider Styling */
        input[type=range]::-webkit-slider-thumb {
            background: var(--bs-warning);
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-body position-relative" style="overflow-x: hidden;">

<!-- Navbar dengan Toggle Dark Mode -->
<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 position-absolute w-100 z-3" data-aos="fade-down">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-black fs-4 text-body" href="#">
            <i class="bi bi-cup-hot-fill text-warning me-2"></i>pakaiapp<span class="text-secondary">.online</span>
        </a>
        <button id="themeToggle" class="btn btn-outline-secondary rounded-circle" title="Ganti Tema">
            <i class="bi bi-moon-stars-fill"></i>
        </button>
    </div>
</nav>

<!-- HERO SECTION: Typed.js & Chat UI -->
<section class="hero-section position-relative pt-5 mt-5 mb-5 overflow-hidden">
    <!-- Dekorasi Bulat Blur -->
    <div class="position-absolute rounded-circle bg-warning opacity-25 blob-shape"
         style="width: 500px; height: 500px; filter: blur(80px); top: -10%; left: -5%; z-index: 0;"></div>

    <div class="container position-relative z-1 mt-4">
        <div class="row align-items-center">

            <!-- Teks Kiri -->
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0" data-aos="fade-right">
                <span
                    class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-25 mb-3 pulse-badge">
                    <i class="bi bi-stars me-1"></i> Revolusi UMKM F&B
                </span>
                <h1 class="display-4 fw-black mb-3 text-body" style="letter-spacing: -1px;">
                    Kasir Pintar, <br>
                    <span class="text-gradient" id="typed-text"></span>
                </h1>
                <p class="lead text-secondary opacity-75 mb-4">
                    Sistem SaaS berbasis *Cloud* dengan arsitektur Varian Harga cerdas. Dirancang khusus oleh *Indie
                    Programmer* agar UMKM bisa *Go Digital* tanpa dipalak biaya bulanan.
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="#simulasi"
                       class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm btn-hover-grow text-dark">
                        <i class="bi bi-calculator me-2"></i>Hitung Untungmu
                    </a>
                </div>
            </div>

            <!-- Chat Kanan (Glassmorphism) -->
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="card glass-card shadow-lg rounded-4 overflow-hidden"
                     style="border-top: 4px solid var(--bs-warning);">
                    <div class="bg-body-tertiary p-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center position-relative"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-robot text-primary fs-5"></i>
                                <span
                                    class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Pakaiapp AI</h6>
                                <small class="text-secondary" style="font-size: 11px;">Merespon dalam 1 detik</small>
                            </div>
                        </div>
                        <i class="bi bi-three-dots-vertical text-secondary"></i>
                    </div>

                    <div class="p-4 bg-body" id="chat-container"
                         style="height: 400px; overflow-y: auto; scroll-behavior: smooth;">
                        <!-- Chat mulai masuk via JS nanti -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- INTERACTIVE SIMULATOR (OP Feature!) -->
<section id="simulasi" class="py-5 bg-primary bg-opacity-10 position-relative">
    <div class="container py-5 text-center">
        <h2 class="fw-black text-body mb-2" data-aos="zoom-in">Simulasi Anti-Buntung</h2>
        <p class="text-secondary mb-5" data-aos="zoom-in" data-aos-delay="100">Buktikan sendiri seberapa hemat pakai
            sistem potong Rp 300 / transaksi sukses.</p>

        <div class="card bg-body border-0 shadow-sm rounded-4 p-4 p-md-5 mx-auto" style="max-width: 800px;"
             data-aos="fade-up">
            <h5 class="fw-bold mb-4">Hari ini toko kamu dapat berapa pesanan?</h5>

            <input type="range" class="form-range" id="trxSlider" min="0" max="100" value="15" step="1">
            <div class="d-flex justify-content-between text-secondary small fw-bold mt-2">
                <span>0 (Tutup/Sepi)</span>
                <span>100+ (Rame Parah!)</span>
            </div>

            <div class="mt-5 text-center">
                <h1 class="display-1 fw-black text-primary" id="trxDisplay">15</h1>
                <p class="text-secondary fw-bold text-uppercase tracking-wider">Transaksi Sukses Hari Ini</p>
            </div>

            <hr class="my-4 text-secondary opacity-25">

            <div class="row text-center g-4">
                <div class="col-md-6 border-end">
                    <p class="mb-1 text-secondary">Biaya Pakaiapp Hari Ini</p>
                    <h3 class="fw-black text-success" id="costPakaiapp">Rp 4.500</h3>
                    <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Adil sesuai omzet</small>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-secondary">Biaya App Langganan Bulanan</p>
                    <h3 class="fw-black text-danger">Rp 6.600 <span class="fs-6 text-muted">/hari</span></h3>
                    <small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Toko sepi tetap bayar</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION 3D (Vanilla Tilt) -->
<section class="py-5 bg-body border-bottom">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-black text-body">Fitur Enterprise, Harga UMKM</h2>
            <p class="text-secondary">Arsitektur *backend* yang matang, bukan sekadar CRUD biasa.</p>
        </div>

        <div class="row g-4">
            <!-- 3D Card 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-primary mb-3 display-4"><i class="bi bi-diagram-3-fill"></i></div>
                    <h4 class="fw-bold">Varian Dinamis</h4>
                    <p class="text-secondary small mb-0">Manajemen harga berdasarkan *Size* atau Tipe. Relasi tabel
                        bersih, tanpa duplikasi data menu. Sangat ringan saat dirender.</p>
                </div>
            </div>
            <!-- 3D Card 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-warning mb-3 display-4"><i class="bi bi-qr-code-scan"></i></div>
                    <h4 class="fw-bold">QR Self-Order</h4>
                    <p class="text-secondary small mb-0">Cetak QR di tiap meja. Pelanggan scan, pesan, dan bisa langsung
                        diarahkan ke *Payment Gateway*. Kasir tinggal pantau layar.</p>
                </div>
            </div>
            <!-- 3D Card 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-success mb-3 display-4"><i class="bi bi-wallet2"></i></div>
                    <h4 class="fw-bold">Top-up Fleksibel</h4>
                    <p class="text-secondary small mb-0">Sistem dompet digital (*Wallet*). Top-up mulai Rp 15.000 saja
                        sudah bisa untuk 50 transaksi. Uang tidak akan pernah hangus.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="py-5 bg-body-tertiary">
    <div class="container py-5">
        <h2 class="fw-black text-center mb-5" data-aos="fade-up">Pertanyaan Sering Muncul</h2>

        <div class="accordion accordion-flush bg-body rounded-4 shadow-sm" id="faqAccordion" data-aos="fade-up"
             data-aos-delay="100">
            <div class="accordion-item rounded-top-4 border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq1">
                        Kalau toko tutup sebulan, saldo saya hilang nggak?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Tentu tidak! Tidak ada sistem *reset* atau langganan hangus. Uang yang kamu top-up menjadi
                        kredit transaksi abadi.
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq2">
                        Gimana kalau pesanan pelanggan dibatalkan?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Sistem kami sangat adil. Saldo Rp 300 hanya terpotong saat status pesanan berubah menjadi
                        "Selesai/Dibayar". Jika batal, tidak ada potongan apapun.
                    </div>
                </div>
            </div>
            <div class="accordion-item rounded-bottom-4 border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq3">
                        Apakah aplikasinya harus di-download di Playstore?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Pakaiapp adalah aplikasi berbasis *Web SaaS (PWA)*. Cukup buka lewat *browser* di HP, Tablet,
                        atau Laptop manapun, lalu "Add to Homescreen". Lebih ringan tanpa menuhin memori!
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-body py-4 border-top">
    <div class="container text-center">
        <p class="text-secondary small mb-0">&copy; {{ date('Y') }} pakaiapp.online. Solusi Digital dari <span
                class="fw-bold text-primary">
                <a href="https://www.ngopikode.com" target="_blank"
                   class="text-decoration-none text-primary">ngopikode.</a>
            </span>.</p>
    </div>
</footer>

<!-- Floating WhatsApp Only -->
<a href="https://wa.me/6285172441544" target="_blank" class="floating-wa shadow-lg" title="Tanya Admin">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- ==============================================
     EXTERNAL LIBRARIES & OP SCRIPTS
=============================================== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Initialize Animasi Scroll (AOS)
    AOS.init({once: true, offset: 50});

    // 2. Initialize Typed.js (Efek Ngetik Keren)
    new Typed('#typed-text', {
        strings: ['Bayar Pas Ada Transaksi.', 'Tanpa Biaya Bulanan.', 'Solusi Adil Untuk UMKM.', 'Bikin Operasional Rapi.'],
        typeSpeed: 50,
        backSpeed: 30,
        loop: true,
        backDelay: 2000
    });

    // 3. Dark Mode Toggle Script
    const themeBtn = document.getElementById('themeToggle');
    const htmlRoot = document.getElementById('html-root');
    const icon = themeBtn.querySelector('i');

    themeBtn.addEventListener('click', () => {
        if (htmlRoot.getAttribute('data-bs-theme') === 'light') {
            htmlRoot.setAttribute('data-bs-theme', 'dark');
            icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            icon.classList.add('text-warning');
        } else {
            htmlRoot.setAttribute('data-bs-theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            icon.classList.remove('text-warning');
        }
    });

    // 4. Kalkulator Interaktif Logic
    const slider = document.getElementById('trxSlider');
    const trxDisplay = document.getElementById('trxDisplay');
    const costPakaiapp = document.getElementById('costPakaiapp');

    slider.addEventListener('input', function () {
        const trx = parseInt(this.value);
        trxDisplay.innerText = trx;

        // Rp 300 / trx
        const cost = trx * 300;

        if (trx === 0) {
            costPakaiapp.innerText = 'GRATIS!';
            costPakaiapp.classList.replace('text-success', 'text-warning');
        } else {
            costPakaiapp.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            costPakaiapp.classList.replace('text-warning', 'text-success');
        }
    });

    // 5. Chat Bot UI & Logic (Lebih "Hidup")
    let userData = {jenisBisnis: '', jumlahCabang: '', namaToko: '', namaOwner: '', noWa: ''};
    const chatContainer = document.getElementById('chat-container');

    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const html = `
            <div id="${id}" class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm">
                <div class="typing-indicator"><span></span><span></span><span></span></div>
            </div><div class="w-100" id="clear-${id}"></div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    function removeTypingIndicator(id) {
        document.getElementById(id).remove();
        document.getElementById('clear-' + id).remove();
    }

    function appendUserBubble(text) {
        const html = `<div class="d-flex justify-content-end mb-4"><div class="chat-bubble-user p-3 rounded-4 bg-primary text-white shadow-sm">${text}</div></div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function appendBotBubble(text, delay = 800) {
        const typingId = showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator(typingId);
            const html = `<div class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm" style="max-width: 85%;">${text}</div><div class="w-100"></div>`;
            chatContainer.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }, delay);
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Alur Chat Awal
    setTimeout(() => {
        appendBotBubble('Halo! 👋 Saya asisten virtual dari Pakaiapp.');
        setTimeout(() => {
            appendBotBubble('Capek ya bayar sistem kasir bulanan padahal toko kadang sepi?');
            setTimeout(() => {
                const options = `
                <div class="d-flex gap-2 flex-wrap mb-4" id="step1-options">
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(1, 'F&B (Cafe/Resto)')">🍔 F&B (Cafe/Resto)</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(1, 'Retail (Toko/Kelontong)')">🛒 Retail (Toko/Kelontong)</button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', options);
                scrollToBottom();
            }, 1000);
        }, 1200);
    }, 500);

    function nextStep(currentStep, value) {
        if (currentStep === 1) {
            userData.jenisBisnis = value;
            document.getElementById('step1-options').remove();
            appendUserBubble(value);

            appendBotBubble(`Pilihan mantap! Bisnis ${value} emang butuh pencatatan varian yang rapi.`);
            setTimeout(() => {
                appendBotBubble(`Berapa banyak outlet yang kamu kelola sekarang?`);
                const options = `
                <div class="d-flex gap-2 flex-wrap mb-4 mt-2" id="step2-options">
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Baru 1 Outlet')">Baru 1 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, '2 - 5 Outlet')">2 - 5 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Lebih dari 5')">Lebih dari 5</button>
                </div>`;
                setTimeout(() => {
                    chatContainer.insertAdjacentHTML('beforeend', options);
                    scrollToBottom();
                }, 1200);
            }, 1000);
        } else if (currentStep === 2) {
            userData.jumlahCabang = value;
            document.getElementById('step2-options').remove();
            appendUserBubble(value);

            appendBotBubble(`Siap! Terakhir nih, biar Pakaiapp kamu bisa langsung dibuatin *database*-nya. Isi form mini ini ya! 🚀`);

            setTimeout(() => {
                const formHTML = `
                <div class="card border-1 border-primary bg-body-tertiary rounded-4 p-3 mb-4 mt-2 shadow-sm glass-card" id="register-form">
                    <div class="mb-3">
                        <input type="text" id="inputToko" class="form-control form-control-sm rounded-3 bg-body" placeholder="Nama Toko (ex: Warung 3 Saudara)">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="inputNama" class="form-control form-control-sm rounded-3 bg-body" placeholder="Nama Kamu">
                    </div>
                    <div class="mb-3">
                        <input type="number" id="inputWa" class="form-control form-control-sm rounded-3 bg-body" placeholder="No. WhatsApp (08xxxx)">
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm chat-btn" onclick="submitToTelegram()" id="btnSubmit">
                        <i class="bi bi-rocket-takeoff-fill me-2"></i>Kirim & Buat Akun!
                    </button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', formHTML);
                scrollToBottom();
            }, 1200);
        }
    }

    // 6. Submit Data pakai SweetAlert (Lebih Keren dari Alert Biasa!)
    function submitToTelegram() {
        userData.namaToko = document.getElementById('inputToko').value;
        userData.namaOwner = document.getElementById('inputNama').value;
        userData.noWa = document.getElementById('inputWa').value;

        if (!userData.namaToko || !userData.namaOwner || !userData.noWa) {
            Swal.fire({
                icon: 'warning',
                title: 'Eits, ada yang kosong',
                text: 'Isi semua datanya dulu dong biar kami gampang hubunginya!',
                confirmButtonColor: 'var(--bs-primary)'
            });
            return;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Menghubungi Server...`;
        btn.disabled = true;

        fetch('/api/send-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(userData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('register-form').remove();
                    appendUserBubble('Data sudah dikirim!');

                    // Sweet Alert Success 🎉
                    Swal.fire({
                        icon: 'success',
                        title: 'Boom! Berhasil 🚀',
                        text: `Data ${userData.namaToko} sudah masuk ke sistem kami.`,
                        confirmButtonColor: 'var(--bs-success)',
                        background: 'var(--bs-body-bg)',
                        color: 'var(--bs-body-color)'
                    });

                    appendBotBubble(`🎉 Asik! Data *${userData.namaToko}* sudah kami terima. Tim Pakaiapp (Ngopikode) akan segera nge-WA kamu untuk serah terima akun. *Stay tuned*!`, 1000);
                } else {
                    throw new Error('Gagal dari server');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh Error 😭',
                    text: 'Koneksi ke server gagal. Coba hubungi via tombol WhatsApp mengambang ya!',
                    confirmButtonColor: 'var(--bs-danger)'
                });
                btn.innerHTML = `<i class="bi bi-rocket-takeoff-fill me-2"></i>Coba Lagi`;
                btn.disabled = false;
            });
    }
</script>
</body>
</html>
