document.addEventListener('DOMContentLoaded', function () {
    /* ---- INIT ---- */
    if (typeof AOS !== 'undefined') AOS.init({ once: true, duration: 600, offset: 40 });

    /* ---- SMOOTH SCROLL for anchor links ---- */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ---- KALKULATOR ---- */
    const slider      = document.getElementById('trxSlider');
    const trxDisplay  = document.getElementById('trxDisplay');
    const costEl      = document.getElementById('costPakaiapp');
    const costNote    = document.getElementById('costNote');
    const unlimitedEl = document.getElementById('unlimitedBadge');

    if (slider) {
        slider.addEventListener('input', function () {
        const trx = parseInt(this.value);
        trxDisplay.textContent = trx.toLocaleString('id-ID');

        let cost = trx * 300;
        const isUnlimited = cost >= 150000;
        if (isUnlimited) cost = 150000;

        if (trx === 0) {
            costEl.textContent = 'GRATIS!';
            costEl.style.color = 'var(--accent)';
            costNote.textContent = 'Tidak ada transaksi = tidak ada biaya.';
            unlimitedEl.style.display = 'none';
        } else if (isUnlimited) {
            costEl.textContent = 'Rp 150.000';
            costEl.style.color = '#fff';
            costNote.textContent = 'Maks. biaya per bulan — sisanya GRATIS tak terbatas!';
            unlimitedEl.style.display = 'inline-flex';
        } else {
            costEl.textContent = 'Rp ' + cost.toLocaleString('id-ID');
            costEl.style.color = 'var(--accent)';
            costNote.textContent = 'Rp 300 × ' + trx.toLocaleString('id-ID') + ' transaksi';
            unlimitedEl.style.display = 'none';
        }
    });
}
    /* ---- FAQ TOGGLE ---- */
    window.toggleFaq = function (btn) {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    };
}); // end DOMContentLoaded

const themeToggle = document.getElementById('theme-toggle');
const htmlRoot = document.getElementById('html-root');
const themeIcon = document.getElementById('theme-icon');

if (themeToggle && htmlRoot && themeIcon) {
    themeToggle.addEventListener('click', () => {
        if (htmlRoot.classList.contains('dark')) {
            htmlRoot.classList.remove('dark');
            htmlRoot.setAttribute('data-bs-theme', 'light');
            themeIcon.classList.remove('bi-brightness-high');
            themeIcon.classList.add('bi-moon-fill');
            localStorage.setItem('theme', 'light');
        } else {
            htmlRoot.classList.add('dark');
            htmlRoot.setAttribute('data-bs-theme', 'dark');
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-brightness-high');
            localStorage.setItem('theme', 'dark');
        }
    });

    if (localStorage.getItem('theme') === 'light') {
        htmlRoot.classList.remove('dark');
        htmlRoot.setAttribute('data-bs-theme', 'light');
        themeIcon.classList.remove('bi-brightness-high');
        themeIcon.classList.add('bi-moon-fill');
    }
}

// --- FAB + GUIDED TOUR ---
window.toggleFab = function() {
    const menu = document.getElementById('fabMenu');
    const btn = document.getElementById('fabMain');
    const icon = document.getElementById('fabIcon');
    const nudge = document.getElementById('fabNudge');
    const isOpen = menu.classList.contains('open');
    if (isOpen) {
        menu.classList.remove('open');
        btn.classList.remove('open');
        icon.className = 'bi bi-headset';
    } else {
        menu.classList.add('open');
        btn.classList.add('open');
        icon.className = 'bi bi-x-lg';
        // Hide nudge when FAB opens
        if (nudge) nudge.classList.remove('show');
    }
};

// Nudge bubble: auto-show after 4s, dismissed by user
document.addEventListener('DOMContentLoaded', function() {
    const nudge = document.getElementById('fabNudge');
    if (!nudge) return; // Not on welcome page

    // Show once per 24 hours using localStorage timestamp
    const lastDismissed = localStorage.getItem('fabNudgeTs');
    const oneDayMs = 24 * 60 * 60 * 1000;
    if (lastDismissed && (Date.now() - parseInt(lastDismissed)) < oneDayMs) return;

    setTimeout(function() {
        nudge.classList.add('show');
        // Auto-hide after 10 seconds
        setTimeout(function() { nudge.classList.remove('show'); }, 10000);
    }, 3500);
});

window.dismissNudge = function() {
    const nudge = document.getElementById('fabNudge');
    if (nudge) nudge.classList.remove('show');
    localStorage.setItem('fabNudgeTs', Date.now().toString());
};

// Close FAB when clicking outside
document.addEventListener('click', function(e) {
    const fab = document.getElementById('fabContainer');
    if (fab && !fab.contains(e.target)) {
        const menu = document.getElementById('fabMenu');
        const btn = document.getElementById('fabMain');
        const icon = document.getElementById('fabIcon');
        if (menu && menu.classList.contains('open')) {
            menu.classList.remove('open');
            btn.classList.remove('open');
            icon.className = 'bi bi-headset';
        }
    }
});

// Tour steps definition
const tourSteps = [
    {
        selector: '.hero',
        title: '👋 Selamat datang di Pakaiapp!',
        msg: 'Ini adalah <strong>halaman utama</strong> Pakaiapp — kasir berbasis web untuk UMKM. Anda bisa langsung daftar gratis dan toko siap dalam 2 menit!'
    },
    {
        selector: '#cara-daftar',
        title: '⚡ Cara Kerja',
        msg: 'Hanya <strong>3 langkah mudah</strong> untuk mulai jualan: Daftar → Input Menu → Langsung Terima Order. Tidak perlu install aplikasi apapun!'
    },
    {
        selector: '#fitur',
        title: '🗂️ Fitur Lengkap',
        msg: 'Semua fitur yang Anda butuhkan sudah tersedia: <strong>Kasir Real-Time, QR Self-Order, QRIS & E-Wallet, Laporan Analitik, Multi-Staf</strong>, dan masih banyak lagi!'
    },
    {
        selector: '.feat-card.feat-card-accent',
        title: '🧾 Kasir Web Real-Time',
        msg: '<strong>Proses transaksi</strong> dari browser HP atau PC Anda. Semua data sinkron ke cloud — seluruh staf bisa akses secara bersamaan!'
    },
    {
        selector: '#harga',
        title: '💰 Harga Super Terjangkau',
        msg: 'Hanya <strong>Rp 300 per transaksi sukses</strong>. Tidak ada biaya bulanan, tidak ada kontrak. Cukup bayar kalau ada penjualan!'
    },
    {
        selector: '.calc-card',
        title: '🧮 Simulasi Biaya',
        msg: 'Geser slider untuk menghitung estimasi biaya Anda per bulan. Setelah tagihan mencapai <strong>Rp 150.000</strong>, semua transaksi berikutnya <strong>GRATIS!</strong>'
    },
    {
        selector: '#cta-hero-register',
        title: '🚀 Siap Mulai?',
        msg: 'Klik tombol <strong>"Buat Toko Sekarang"</strong> untuk mendaftar gratis sekarang juga. Proses hanya 2 menit dan toko Anda langsung aktif!'
    }
];

let tourCurrentIndex = 0;
let tourHighlightedEl = null;

window.startTour = function() {
    // Close FAB menu first
    const menu = document.getElementById('fabMenu');
    const btn = document.getElementById('fabMain');
    const icon = document.getElementById('fabIcon');
    if (menu) { menu.classList.remove('open'); btn.classList.remove('open'); icon.className = 'bi bi-headset'; }

    tourCurrentIndex = 0;
    document.getElementById('tourMessages').innerHTML = '';
    document.getElementById('tourOverlay').classList.add('active');
    const popup = document.getElementById('tourPopup');
    popup.classList.add('open');
    tourShowStep(0);
};

window.tourNext = function() {
    tourCurrentIndex++;
    if (tourCurrentIndex >= tourSteps.length) {
        closeTour();
        return;
    }
    tourShowStep(tourCurrentIndex);
};

function tourShowStep(index) {
    const step = tourSteps[index];
    const nextBtn = document.getElementById('tourNextBtn');
    const isLast = index === tourSteps.length - 1;
    nextBtn.innerHTML = isLast
        ? '<i class="bi bi-check-lg"></i> Selesai!'
        : '<i class="bi bi-chevron-right"></i> Selanjutnya';

    // Remove previous highlight
    if (tourHighlightedEl) {
        tourHighlightedEl.classList.remove('tour-highlight-el');
        tourHighlightedEl = null;
    }

    const target = document.querySelector(step.selector);
    if (target) {
        // Scroll to element
        setTimeout(() => {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);

        // Highlight after scroll
        setTimeout(() => {
            target.classList.add('tour-highlight-el');
            tourHighlightedEl = target;

            // Position tooltip near element
            const rect = target.getBoundingClientRect();
            const tooltip = document.getElementById('tourTooltip');
            tooltip.textContent = step.title;
            tooltip.className = 'tour-spotlight-tooltip show';

            const tipTop = rect.top - 44;
            const tipLeft = Math.min(rect.left, window.innerWidth - 270);
            tooltip.style.top = (tipTop < 10 ? rect.bottom + 8 : tipTop) + 'px';
            tooltip.style.left = Math.max(8, tipLeft) + 'px';
        }, 400);
    }

    // Add message
    addTourMessage(step.msg);
}

function addTourMessage(html) {
    const area = document.getElementById('tourMessages');
    const msg = document.createElement('div');
    msg.className = 'tour-msg';
    msg.innerHTML = `
        <div class="tour-msg-avatar"><i class="bi bi-stars"></i></div>
        <div class="tour-msg-bubble">${html}</div>
    `;
    area.appendChild(msg);
    setTimeout(() => { area.scrollTop = area.scrollHeight; }, 50);
}

window.closeTour = function() {
    document.getElementById('tourOverlay').classList.remove('active');
    const popup = document.getElementById('tourPopup');
    popup.style.opacity = '0';
    popup.style.transform = 'translateY(20px)';
    setTimeout(() => {
        popup.classList.remove('open');
        popup.style.opacity = '';
        popup.style.transform = '';
    }, 350);

    if (tourHighlightedEl) {
        tourHighlightedEl.classList.remove('tour-highlight-el');
        tourHighlightedEl = null;
    }
    const tooltip = document.getElementById('tourTooltip');
    if (tooltip) tooltip.className = 'tour-spotlight-tooltip';
};

window.closeTourOverlay = function(e) {
    if (e.target === document.getElementById('tourOverlay')) {
        closeTour();
    }
};

// --- LOGIN LOGIC ---
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('formLogin')) {
        document.getElementById('formLogin').addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmitLogin');
            const orig = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mencari...';
            btn.disabled = true;

            const loginInput = document.getElementById('login_input').value;
            const storeListContainer = document.getElementById('storeListContainer');
            const storeList = document.getElementById('storeList');

            storeListContainer.style.display = 'none';
            storeList.innerHTML = '';

            fetch('/api/central-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ login_input: loginInput })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.type === 'subdomain') {
                        window.location.href = data.redirect_url;
                    } else if (data.type === 'email') {
                        storeListContainer.style.display = 'block';
                        data.stores.forEach(store => {
                            const card = document.createElement('div');
                            card.className = 'store-card';
                            
                            const info = document.createElement('div');
                            info.className = 'store-info';
                            
                            const name = document.createElement('span');
                            name.className = 'store-name';
                            name.textContent = store.store_name;
                            
                            const sub = document.createElement('span');
                            sub.className = 'store-subdomain';
                            sub.textContent = store.tenant_id + '.pakaiapp.online';
                            
                            info.appendChild(name);
                            info.appendChild(sub);
                            
                            const link = document.createElement('a');
                            link.className = 'btn-open-store';
                            link.href = store.url;
                            link.textContent = 'Buka Dashboard';
                            
                            card.appendChild(info);
                            card.appendChild(link);
                            
                            storeList.appendChild(card);
                        });
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Pencarian Gagal', text: data.message || 'Terjadi kesalahan sistem.', background: 'var(--surface)', color: 'var(--text)' });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Gagal menghubungi server central.', background: 'var(--surface)', color: 'var(--text)' });
            })
            .finally(() => {
                btn.innerHTML = orig;
                btn.disabled = false;
            });
        });
    }
});

// --- REGISTER AI PROMPT LOGIC ---
document.addEventListener('DOMContentLoaded', async () => {
    if (document.getElementById('promptMain')) {
        const questionArea = document.getElementById('questionArea');
        const aiQuestion = document.getElementById('aiQuestion');
        const aiSubQuestion = document.getElementById('aiSubQuestion');
        const inputContainer = document.getElementById('inputContainer');
        const chatInput = document.getElementById('chatInput');
        const btnSend = document.getElementById('btnSend');
        const choicesContainer = document.getElementById('choicesContainer');
        const btnStepBack = document.getElementById('btnStepBack');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');

        let currentStepIndex = 0;
        let formData = { namaToko: '', jenisBisnis: '', namaOwner: '', noWa: '', email: '', password: '', paket: '', payment_method: '' };
        let isEmailVerified = false;
        let isProcessing = false;
        let toastTimeout;
        let modalCallback = null;

        // Sequence of steps
        const steps = [
            { id: 'TanyaNama', q: 'Halo! Siapa nama toko atau bisnis Anda?', sub: 'Mari kita siapkan kasir cerdas Anda dalam hitungan detik.', type: 'text', placeholder: 'Ketik nama tokomu di sini...' },
            { id: 'TanyaBisnis', q: 'Termasuk dalam kategori apakah bisnis Anda?', sub: 'Untuk menyesuaikan fitur sistem dengan kebutuhan Anda.', type: 'choice', choices: [{l: 'F&B (Resto, Cafe, Warung)', v: 'F&B (Resto/Cafe)'}, {l: 'Retail (Baju, Kelontong)', v: 'Retail (Toko/Butik)'}] },
            { id: 'TanyaOwner', q: 'Siapa nama pemilik bisnis hebat ini?', sub: 'Nama lengkap Anda, agar kami bisa menyapa dengan benar.', type: 'text', placeholder: 'Ketik nama lengkap Anda...' },
            { id: 'TanyaWa', q: 'Berapa nomor WhatsApp aktif Anda?', sub: 'Untuk mengirimkan informasi penting terkait toko.', type: 'tel', placeholder: 'Contoh: 08123456789' },
            { id: 'TanyaEmail', q: 'Apa alamat email aktif Anda?', sub: 'Kami akan mengirimkan kode verifikasi (OTP) ke email ini.', type: 'email', placeholder: 'Contoh: nama@email.com' },
            { id: 'TanyaOTP', q: 'Masukkan 6 angka OTP', sub: 'Cek kotak masuk atau folder spam email Anda.', type: 'number', placeholder: 'Ketik 6 angka OTP di sini...' },
            { id: 'TanyaPassword', q: 'Buat kata sandi yang kuat', sub: 'Minimal 6 karakter untuk melindungi akun Anda.', type: 'password', placeholder: 'Minimal 6 karakter...' },
            { id: 'TanyaPaket', q: 'Pilih paket langganan Anda', sub: 'Pilih yang paling sesuai dengan kebutuhan bisnis.', type: 'choice', choices: [{l: 'Gratis (Rp 0)', v: 'free'}, {l: 'Santai (Rp 50.000/bln)', v: 'santai'}, {l: 'Premium (Rp 150.000/bln)', v: 'premium'}] },
            { id: 'TanyaPayment', q: 'Metode Pembayaran', sub: 'Pilih metode pembayaran yang Anda inginkan.', type: 'choice', choices: [{l: 'QRIS / E-Wallet', v: 'NQ'}, {l: 'Transfer / VA', v: 'BC'}, {l: 'Manual (Bantuan WA Admin)', v: 'manual'}] }
        ];

        function showToast(message) {
            document.getElementById('toastMsg').innerText = message;
            const toast = document.getElementById('customToast');
            toast.classList.add('show');
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => toast.classList.remove('show'), 3000);
        }

        window.showCustomAlert = function(type, title, text, callback = null, btnText = 'Tutup') {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalDesc').innerText = text;
            document.getElementById('modalBtn').innerText = btnText;

            const icon = document.getElementById('modalIcon');
            if (type === 'success') icon.innerHTML = '<i class="bi bi-check-circle-fill" style="color: var(--success);"></i>';
            else if (type === 'error') icon.innerHTML = '<i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>';
            else icon.innerHTML = '<i class="bi bi-info-circle-fill" style="color: #3b82f6;"></i>';

            modalCallback = callback;
            document.getElementById('customModal').classList.add('show');
        };

        window.closeCustomAlert = function() {
            document.getElementById('customModal').classList.remove('show');
            if (modalCallback) { modalCallback(); modalCallback = null; }
        };

        function showLoading(text) {
            loadingText.innerText = text;
            loadingOverlay.style.display = 'flex';
        }

        function hideLoading() {
            loadingOverlay.style.display = 'none';
        }

        async function askStep(index) {
            if (index < 0) return;
            const step = steps[index];
            currentStepIndex = index;
            
            // Fade out
            questionArea.classList.add('fade-out');
            inputContainer.style.display = 'none';
            choicesContainer.style.display = 'none';
            btnStepBack.style.display = index > 0 ? 'inline-flex' : 'none';

            await new Promise(r => setTimeout(r, 400)); // wait for fade out
            
            // Update text
            aiQuestion.innerText = step.q;
            aiSubQuestion.innerText = step.sub;
            chatInput.value = '';

            if (step.type === 'choice') {
                if (step.id === 'TanyaPayment') {
                    choicesContainer.innerHTML = `
                        <div class="payment-methods-panel" style="width: 100%; display: flex; flex-direction: column; gap: 16px; margin-top: 10px; max-width: 540px; margin-left: auto; margin-right: auto; text-align: left;">
                            
                            <!-- Opsi 1: Manual -->
                            <div class="payment-card-option manual-opt" onclick="handleChoiceClick('manual')">
                                <div class="payment-card-header">
                                    <div class="payment-card-icon wa-icon">
                                        <i class="bi bi-whatsapp"></i>
                                    </div>
                                    <div class="payment-card-content">
                                        <h4 class="payment-card-title">Transfer Manual (Bantuan WA Admin)</h4>
                                        <p class="payment-card-desc">Konfirmasi pembayaran manual secara personal. Admin aktif 5-10 menit.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi 2: Midtrans -->
                            <div class="payment-card-option midtrans-opt" onclick="handleChoiceClick('midtrans')">
                                <div class="payment-card-header">
                                    <div class="payment-card-icon midtrans-icon">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </div>
                                    <div class="payment-card-content">
                                        <div class="payment-card-title-row">
                                            <h4 class="payment-card-title">Pembayaran Instan (Midtrans)</h4>
                                            <span class="sandbox-badge midtrans-badge">Mode Uji Coba (Sandbox Midtrans)</span>
                                        </div>
                                        <p class="payment-card-desc">Bayar langsung menggunakan e-wallet atau VA. Pembayaran instan terverifikasi otomatis.</p>
                                        <div class="sandbox-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <span>Pembayaran sedang dalam tahap simulasi. Jangan gunakan data asli.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi 3: Duitku -->
                            <div class="payment-card-option duitku-opt-card" id="optionDuitkuCard" onclick="expandDuitkuOptions(event)">
                                <div class="payment-card-header">
                                    <div class="payment-card-icon duitku-icon">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div class="payment-card-content" style="width: 100%;">
                                        <div class="payment-card-title-row">
                                            <h4 class="payment-card-title">Transfer & E-Wallet Otomatis (Duitku)</h4>
                                            <span class="sandbox-badge duitku-badge">Mode Uji Coba (Sandbox Duitku)</span>
                                        </div>
                                        <p class="payment-card-desc">Bayar otomatis menggunakan QRIS, ShopeePay, OVO, LinkAja, atau berbagai Virtual Account.</p>
                                        <div class="sandbox-warning" style="margin-bottom: 12px;">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <span>Pembayaran sedang dalam tahap simulasi. Jangan gunakan data asli.</span>
                                        </div>

                                        <!-- Sub-opsi List (Duitku Payment Methods) -->
                                        <div id="duitkuMethodsContainer" class="duitku-methods-container" style="display: none;" onclick="event.stopPropagation()">
                                            <div class="duitku-methods-header">
                                                <span class="duitku-methods-label">Pilih Saluran Pembayaran Duitku:</span>
                                                <span id="loadingDuitkuMethods" class="duitku-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;"></span> Memuat...</span>
                                            </div>
                                            <div id="duitkuMethodsGrid" class="duitku-methods-grid">
                                                <!-- Metode pembayaran dimasukkan via Javascript -->
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    `;
                } else {
                    choicesContainer.innerHTML = step.choices.map(c => `
                        <button class="btn-choice-pill" onclick="handleChoiceClick('${c.v}')">
                            ${c.l}
                        </button>
                    `).join('');
                }
                choicesContainer.style.display = 'flex';
            } else {
                chatInput.type = step.type;
                chatInput.placeholder = step.placeholder;
                if (step.type === 'number' && step.id === 'TanyaOTP') chatInput.maxLength = 6;
                else chatInput.removeAttribute('maxLength');
                inputContainer.style.display = 'flex';
            }

            // Fade in
            questionArea.classList.remove('fade-out');
            if (step.type !== 'choice') {
                setTimeout(() => chatInput.focus(), 400);
            }
        }

        btnSend.addEventListener('click', processInput);
        chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') processInput(); });
        
        btnStepBack.addEventListener('click', () => {
            if (isProcessing) return;
            // Go back one step logic
            let prevIndex = currentStepIndex - 1;
            // Skip TanyaOTP if email was verified but we go back past password? No, just keep it simple.
            // If they go back from OTP, they want to change email.
            if (steps[currentStepIndex].id === 'TanyaOTP') {
                isEmailVerified = false; // Reset if they go back to change email
            }
            if (steps[prevIndex].id === 'TanyaPayment') {
                prevIndex = steps.findIndex(s => s.id === 'TanyaPaket');
            }
            askStep(prevIndex);
        });

        async function processInput() {
            if (isProcessing) return;
            const val = chatInput.value.trim();
            const step = steps[currentStepIndex];
            
            if (step.type !== 'choice' && !val) return;

            isProcessing = true;
            btnSend.innerHTML = '<i class="bi bi-three-dots"></i>';
            btnSend.disabled = true;

            if (step.id === 'TanyaNama') {
                formData.namaToko = val;
                askStep(currentStepIndex + 1);
            } else if (step.id === 'TanyaOwner') {
                formData.namaOwner = val;
                askStep(currentStepIndex + 1);
            } else if (step.id === 'TanyaWa') {
                if (val.length < 9 || isNaN(val.replace(/\+/g, ''))) {
                    showToast('Nomor WA tidak valid.');
                } else {
                    formData.noWa = val;
                    askStep(currentStepIndex + 1);
                }
            } else if (step.id === 'TanyaEmail') {
                if (!val.includes('@')) {
                    showToast('Email tidak valid.');
                } else {
                    formData.email = val;
                    showLoading('Mengirimkan OTP ke email Anda...');
                    try {
                        let res = await fetch('/api/request-otp', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({email: formData.email})
                        });
                        let data = await res.json();
                        hideLoading();
                        if (data.status === 'success') {
                            askStep(currentStepIndex + 1);
                        } else {
                            showToast(data.message);
                        }
                    } catch (e) {
                        hideLoading();
                        showToast('Gagal terhubung ke server.');
                    }
                }
            } else if (step.id === 'TanyaOTP') {
                if (val.length !== 6) {
                    showToast('Masukkan 6 digit kode OTP.');
                } else {
                    showLoading('Memverifikasi OTP...');
                    try {
                        let res = await fetch('/api/verify-otp', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({email: formData.email, otp: val})
                        });
                        let data = await res.json();
                        hideLoading();
                        if (data.status === 'success') {
                            isEmailVerified = true;
                            askStep(currentStepIndex + 1);
                        } else {
                            showToast('Kode OTP salah atau kedaluwarsa.');
                        }
                    } catch (e) {
                        hideLoading();
                        showToast('Gagal verifikasi jaringan.');
                    }
                }
            } else if (step.id === 'TanyaPassword') {
                if (val.length < 6) {
                    showToast('Password minimal 6 karakter.');
                } else {
                    formData.password = val;
                    askStep(currentStepIndex + 1);
                }
            }

            isProcessing = false;
            btnSend.innerHTML = '<i class="bi bi-arrow-up-short"></i>';
            btnSend.disabled = false;
        }

        window.handleChoiceClick = async function(value) {
            if (isProcessing) return;
            const step = steps[currentStepIndex];
            
            if (step.id === 'TanyaBisnis') {
                formData.jenisBisnis = value;
                askStep(currentStepIndex + 1);
            } else if (step.id === 'TanyaPaket') {
                formData.paket = value;
                if (value === 'free') {
                    formData.payment_method = 'free';
                    finalizeRegistration();
                } else {
                    askStep(currentStepIndex + 1); // TanyaPayment
                }
            } else if (step.id === 'TanyaPayment') {
                formData.payment_method = value;
                finalizeRegistration();
            }
        };

        async function finalizeRegistration() {
            showLoading('Bagus sekali! Menyiapkan toko Anda...');
            try {
                let res = await fetch('/api/register-tenant', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(formData)
                });
                let data = await res.json();
                hideLoading();
                
                if (data.status === 'success') {
                    showLoading('Selamat! Mengarahkan ke dashboard...');
                    setTimeout(() => window.location.href = data.redirect_url, 1500);
                } else if (data.status === 'manual') {
                    showCustomAlert('info', 'Pendaftaran Dicatat', 'Selesaikan pembayaran manual via WhatsApp.', () => {
                        window.open(data.redirect_url, '_blank'); window.location.href = '/';
                    }, 'Buka WhatsApp');
                } else if (data.status === 'payment_required_duitku') {
                    window.location.href = data.payment_url;
                } else if (data.status === 'payment_required_midtrans') {
                    window.snap.pay(data.snap_token, {
                        onSuccess: () => window.location.href = '/register/status/' + data.invoice_code,
                        onPending: () => window.location.href = '/register/status/' + data.invoice_code,
                        onError: () => showToast('Pembayaran gagal.'),
                        onClose: () => window.location.href = '/register/status/' + data.invoice_code
                    });
                } else {
                    showToast('Oops, terjadi kesalahan: ' + data.message);
                }
            } catch (e) {
                hideLoading();
                showToast('Gagal menghubungi server.');
            }
        }

        window.expandDuitkuOptions = async function(event) {
            event.stopPropagation();
            const container = document.getElementById('duitkuMethodsContainer');
            const grid = document.getElementById('duitkuMethodsGrid');
            const loader = document.getElementById('loadingDuitkuMethods');
            
            // Toggle container
            if (container.style.display === 'block') {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            
            // If already loaded, do not fetch again
            if (grid.children.length > 0) return;
            
            loader.style.display = 'inline-flex';
            try {
                const amount = formData.paket === 'santai' ? 50000 : 150000;
                const res = await fetch('/api/duitku/payment-methods?amount=' + amount);
                const data = await res.json();
                loader.style.display = 'none';
                
                if (data.success && data.data && data.data.length > 0) {
                    grid.innerHTML = data.data.map(m => `
                        <div class="duitku-method-card" onclick="selectDuitkuMethod(event, '${m.paymentMethod}')">
                            ${m.paymentImage ? `<img src="${m.paymentImage}" alt="${m.paymentName}">` : ''}
                            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text); line-height: 1.2;">${m.paymentName}</div>
                            <div style="font-size: 0.62rem; color: var(--text-muted); font-weight: 500;">+Rp ${parseInt(m.fee).toLocaleString('id-ID')}</div>
                        </div>
                    `).join('');
                } else {
                    grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; font-size: 0.78rem; color: var(--red); padding: 10px;">Gagal memuat metode pembayaran Duitku. Silakan coba lagi.</div>`;
                }
            } catch (e) {
                loader.style.display = 'none';
                grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; font-size: 0.78rem; color: var(--red); padding: 10px;">Terjadi kesalahan jaringan.</div>`;
            }
        };

        window.selectDuitkuMethod = function(event, code) {
            event.stopPropagation();
            handleChoiceClick(code);
        };

        // Initialize First Step
        setTimeout(() => askStep(0), 100);
    }
});
