<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pakaiapp.online - Sistem Kasir Pintar Bayar Suka-Suka</title>

    <!-- Animasi AOS & Icons -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite Assets -->
    @vite(['resources/sass/welcome.scss', 'resources/js/app.js'])
</head>
<body class="bg-body position-relative" style="overflow-x: hidden;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 position-absolute w-100 z-3" data-aos="fade-down">
    <div class="container">
        <a class="navbar-brand fw-black fs-4 text-body" href="#">
            <i class="bi bi-cup-hot-fill text-warning me-2"></i>pakaiapp<span class="text-secondary">.online</span>
        </a>
    </div>
</nav>

<!-- Hero Section - Conversational UI -->
<section class="hero-section position-relative pt-5 mt-5 mb-5">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-4" data-aos="fade-up">
                <span
                    class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-25 mb-3">
                    <i class="bi bi-robot me-1"></i> Pendaftaran Interaktif
                </span>
                <h1 class="display-4 fw-black mb-3 text-body">
                    Mari Bangun <span class="text-gradient">Kasir Impianmu.</span>
                </h1>
                <p class="lead text-secondary opacity-75">Jawab beberapa pertanyaan di bawah, dan asisten virtual kami
                    akan menyiapkan sistem kasirmu dalam hitungan detik.</p>
            </div>

            <!-- Area Chat Interaktif -->
            <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-body">
                    <div class="bg-body-tertiary p-3 border-bottom d-flex align-items-center gap-3">
                        <div
                            class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-robot text-primary fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Pakaiapp Assistant</h6>
                            <small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Online</small>
                        </div>
                    </div>

                    <div class="p-4 bg-body" id="chat-container"
                         style="height: 450px; overflow-y: auto; scroll-behavior: smooth;">
                        <!-- Step 1 -->
                        <div
                            class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm">
                            Halo! 👋 Selamat datang di pakaiapp.online. Capek ya bayar langganan kasir bulanan padahal
                            toko kadang sepi?
                        </div>
                        <div class="chat-bubble-bot mb-4 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm"
                             style="animation-delay: 0.5s;">
                            Kasih tau dong, tipe bisnis kamu apa nih?
                        </div>

                        <div class="d-flex gap-2 flex-wrap mb-4" id="step1-options">
                            <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn"
                                    onclick="nextStep(1, 'F&B (Cafe / Resto)')">🍔 F&B (Cafe / Resto)
                            </button>
                            <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn"
                                    onclick="nextStep(1, 'Retail (Toko Kelontong)')">🛒 Retail (Toko Kelontong)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Lengkap Section -->
<section class="py-5 bg-body-tertiary border-top border-bottom">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-warning fw-bold text-uppercase tracking-wider">Fitur Unggulan</span>
            <h2 class="fw-black text-body mt-2">Didesain Spesifik Untuk Bisnismu</h2>
            <p class="text-secondary">Bukan sekadar kasir biasa, sistem ini dibangun agar operasional lebih rapi.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card">
                    <div class="text-primary mb-3 display-5"><i class="bi bi-diagram-3-fill"></i></div>
                    <h5 class="fw-bold">Manajemen Varian Harga</h5>
                    <p class="text-secondary small mb-0">Atur harga berdasarkan ukuran atau tipe tanpa data ganda di
                        tabel. Lebih bersih, lebih cepat ditarik di database.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card">
                    <div class="text-warning mb-3 display-5"><i class="bi bi-qr-code-scan"></i></div>
                    <h5 class="fw-bold">Integrasi QR Order & Bayar</h5>
                    <p class="text-secondary small mb-0">Pelanggan bisa scan menu, order, dan langsung bayar via QRIS
                        atau E-Wallet langsung dari meja mereka.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card">
                    <div class="text-success mb-3 display-5"><i class="bi bi-coin"></i></div>
                    <h5 class="fw-bold">Skema Kredit Rp 300</h5>
                    <p class="text-secondary small mb-0">Hapus biaya server bulanan! Saldo dompetmu hanya dipotong Rp
                        300 per transaksi sukses. Gagal? Saldo aman.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card">
                    <div class="text-danger mb-3 display-5"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                    <h5 class="fw-bold">SaaS Cloud Real-Time</h5>
                    <p class="text-secondary small mb-0">Pantau penjualan, tambah menu, dan cek laporan keuangan dari
                        mana saja secara real-time. Data aman di cloud.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating WhatsApp Only -->
<a href="https://wa.me/628XXXXXXXXXX?text=Halo%20Admin%20pakaiapp,%20saya%20mau%20tanya-tanya%20dulu%20nih."
   target="_blank" class="floating-wa shadow-lg" data-bs-toggle="tooltip" data-bs-placement="left" title="Tanya Admin">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- JavaScript untuk AI Onboarding -->
<script>
    let userData = {
        jenisBisnis: '',
        jumlahCabang: '',
        namaToko: '',
        namaOwner: '',
        noWa: ''
    };

    const chatContainer = document.getElementById('chat-container');

    function appendUserBubble(text) {
        const bubble = `<div class="d-flex justify-content-end mb-4"><div class="chat-bubble-user p-3 rounded-4 bg-primary text-white shadow-sm">${text}</div></div>`;
        chatContainer.insertAdjacentHTML('beforeend', bubble);
        scrollToBottom();
    }

    function appendBotBubble(text) {
        const bubble = `<div class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm" style="max-width: 80%;">${text}</div><div class="w-100"></div>`;
        chatContainer.insertAdjacentHTML('beforeend', bubble);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function nextStep(currentStep, value) {
        if (currentStep === 1) {
            userData.jenisBisnis = value;
            document.getElementById('step1-options').remove();
            appendUserBubble(value);

            setTimeout(() => {
                appendBotBubble(`Pilihan mantap! Bisnis ${value} emang lagi butuh banget sistem yang gesit.`);
                appendBotBubble(`Saat ini, kamu punya berapa cabang/outlet?`);

                const options = `
                <div class="d-flex gap-2 flex-wrap mb-4" id="step2-options">
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Baru 1 Outlet')">Baru 1 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, '2 - 5 Outlet')">2 - 5 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Lebih dari 5')">Lebih dari 5</button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', options);
                scrollToBottom();
            }, 800);
        } else if (currentStep === 2) {
            userData.jumlahCabang = value;
            document.getElementById('step2-options').remove();
            appendUserBubble(value);

            setTimeout(() => {
                appendBotBubble(`Oke, dicatat. Terakhir nih, biar sistem pakaiapp.online kamu bisa langsung dibuatkan aksesnya, isi data ini ya! 🚀`);

                const formHTML = `
                <div class="card border-0 bg-body-tertiary rounded-4 p-3 mb-4 mt-2" id="register-form">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Toko/Usaha</label>
                        <input type="text" id="inputToko" class="form-control form-control-sm rounded-3" placeholder="Contoh: Kopi Senja">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kamu</label>
                        <input type="text" id="inputNama" class="form-control form-control-sm rounded-3" placeholder="Contoh: Budi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">No. WhatsApp</label>
                        <input type="number" id="inputWa" class="form-control form-control-sm rounded-3" placeholder="08xxxx">
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" onclick="submitToTelegram()" id="btnSubmit">
                        <i class="bi bi-rocket-takeoff-fill me-2"></i>Kirim & Buat Akun!
                    </button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', formHTML);
                scrollToBottom();
            }, 800);
        }
    }

    function submitToTelegram() {
        userData.namaToko = document.getElementById('inputToko').value;
        userData.namaOwner = document.getElementById('inputNama').value;
        userData.noWa = document.getElementById('inputWa').value;

        if (!userData.namaToko || !userData.namaOwner || !userData.noWa) {
            alert('Mohon isi semua data ya biar lancar!');
            return;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Memproses...`;
        btn.disabled = true;

        // Tembak endpoint Laravel Backend (Aman dari intipan Token)
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

                    setTimeout(() => {
                        appendBotBubble(`🎉 Keren! Data *${userData.namaToko}* sudah kami terima dengan aman di server.`);
                        appendBotBubble(`Tim pakaiapp akan segera menghubungi WhatsApp kamu (${userData.noWa}) untuk setup *dashboard* dan varian menunya. Ditunggu ya!`);
                    }, 500);
                } else {
                    throw new Error('Gagal dari server');
                }
            })
            .catch(error => {
                alert('Waduh, koneksi error. Coba kirim manual via WA ya.');
                btn.innerHTML = `<i class="bi bi-rocket-takeoff-fill me-2"></i>Coba Lagi`;
                btn.disabled = false;
            });
    }
</script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({once: true, offset: 50});
</script>
</body>
</html>
