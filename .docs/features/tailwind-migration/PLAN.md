be# Rencana Implementasi: Migrasi Bootstrap → Tailwind CSS 3

**Terkait ADR:** [0004-migrate-bootstrap-to-tailwind.md](../../decisions/0004-migrate-bootstrap-to-tailwind.md)  
**Status:** 🔴 Belum Dimulai  
**Estimasi Total:** ~16–22 engineer-days

---

## Ringkasan Perubahan

Migrasi penuh framework CSS dashboard dari Bootstrap 5.3 ke Tailwind CSS 3, termasuk:
- Hapus `app.scss` + Bootstrap CSS → buat `app.css` baru dengan Tailwind directives
- Hapus Bootstrap JS → ganti dengan Alpine.js (sudah ada di project)
- Hapus Bootstrap Icons (`bi bi-*`) → ganti dengan Phosphor Icons (`ph-*`)
- Migrasi dark mode dari `data-bs-theme` → class `dark` Tailwind

---

## Status Progres

| Fase | Deskripsi | Status | File Utama |
|---|---|---|---|
| **Fase 0** | Build System Setup | ✅ Selesai | `vite.config.js`, `app.css`, `tailwind.config.js` |
| **Fase 1** | JS & Dark Mode | ✅ Selesai | `app.js` |
| **Fase 2** | Layout Shell | ✅ Selesai | `app.blade.php` |
| **Fase 3** | Sidebar & Navbar | ✅ Selesai | `sidebar.blade.php`, `navbar.blade.php`, `sidebar-item.blade.php`, `sidebar.php` |
| **Fase 4** | Dashboard Page | ✅ Selesai | `dashboard.blade.php` |
| **Fase 5** | Order & Modals | ⬜ Belum | `order-list.blade.php`, `payment-modal.blade.php`, `order-modal.blade.php` |
| **Fase 6** | POS Cashier | ⬜ Belum | `resto-cashier.blade.php`, `retail-cashier.blade.php`, 9 modal partials |
| **Fase 7** | Halaman Lain | ⬜ Belum | Product, Setting, User, Wallet, Profile, Kitchen |
| **Fase 8** | Components & Cleanup | ⬜ Belum | Global components, hapus Bootstrap dari `package.json` |

> Update status: ⬜ Belum → 🟡 Sedang → ✅ Selesai

---

## Fase 0 — Build System Setup

### Tujuan
Mengganti entri CSS dari SCSS Bootstrap ke Tailwind CSS, tanpa mengubah view apapun dulu.

### File yang diubah

#### `vite.config.js`
```diff
-'resources/sass/app.scss',
+'resources/css/app.css',
```

#### `resources/css/app.css` [BARU]
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Semua custom vars dari app.scss dipindahkan ke sini */
:root {
    --brand-accent:       #10B981;
    --brand-accent-dark:  #059669;
    --brand-accent-light: #34D399;
    --brand-green:        #22C55E;
    --brand-red:          #EF4444;
    --brand-purple:       #A78BFA;
    --brand-sky:          #38BDF8;

    /* Backward compat aliases */
    --brand-espresso: #1E293B;
    --brand-caramel:  #10B981;
    --brand-mocha:    #64748B;
    --brand-latte:    #F8FAFC;

    /* Light mode surface vars */
    --surface:        #FFFFFF;
    --surface-muted:  #F8FAFC;
    --border:         #E2E8F0;
    --foreground:     #0F172A;
    --text-secondary: #475569;
}

.dark {
    --surface:        #0F172A;
    --surface-muted:  #1E293B;
    --border:         rgba(255,255,255,0.1);
    --foreground:     #F8FAFC;
    --text-secondary: #94A3B8;
}

@layer base {
    body { @apply font-sans antialiased; }
}

@layer components {
    /* Semua class custom yang ada di app.scss lama */
    .page-store-name { @apply text-2xl font-bold tracking-tight; }
    .dash-card { @apply rounded-2xl border bg-white dark:bg-slate-900 shadow-sm; }
    /* ... */
}
```

#### `tailwind.config.js` [MODIFIKASI atau BARU]
```js
module.exports = {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/views/**/*.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    accent: '#10B981',
                    dark:   '#059669',
                    light:  '#34D399',
                },
            },
            fontFamily: {
                sans:  ['Inter', 'system-ui', 'sans-serif'],
                serif: ['Plus Jakarta Sans', 'sans-serif'],
            },
        }
    },
    plugins: [],
}
```

---

## Fase 1 — JS & Dark Mode

### File: `resources/js/app.js`

```diff
 import "@phosphor-icons/web/bold";
 import "@phosphor-icons/web/fill";
 import "@phosphor-icons/web/regular";
-// Import all of Bootstrap's JS
-import * as bootstrap from 'bootstrap';
 import NProgress from 'nprogress';
 import Swal from 'sweetalert2';

-window.bootstrap = bootstrap;
 window.Swal = Swal;

 // Dark mode: ganti dari data-bs-theme ke class 'dark'
-document.documentElement.setAttribute('data-bs-theme', theme);
+document.documentElement.classList.toggle('dark', theme === 'dark');

 // Sidebar mobile: ganti dari Offcanvas.getInstance ke Alpine event
-const offcanvasInstance = bootstrap.Offcanvas.getInstance(mobileSidebarEl);
-if (offcanvasInstance) { offcanvasInstance.hide(); }
+window.dispatchEvent(new CustomEvent('close-mobile-sidebar'));
```

---

## Fase 2 — Layout Shell

### File: `resources/views/layouts/app.blade.php`

**Dark mode script:**
```diff
-document.documentElement.setAttribute('data-bs-theme', theme);
+if (theme === 'dark') document.documentElement.classList.add('dark');
```

**Sidebar mobile (Bootstrap Offcanvas → Alpine drawer):**
```html
<!-- SEBELUM: Bootstrap Offcanvas -->
<div class="offcanvas offcanvas-start border-0 shadow d-lg-none" id="mobileSidebar" ...>

<!-- SESUDAH: Alpine Drawer -->
<div x-data="{ open: false }"
     @open-mobile-sidebar.window="open=true"
     @close-mobile-sidebar.window="open=false">
    <!-- Overlay -->
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         @click="open=false"></div>
    <!-- Drawer -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-slate-900 z-50 lg:hidden shadow-xl">
        <livewire:layouts.sidebar elementId="mobile-sidebar-wrapper"/>
    </div>
</div>

<!-- Desktop sidebar: d-none d-lg-block → hidden lg:block -->
<div class="hidden lg:block h-full shrink-0 w-64">
    <livewire:layouts.sidebar elementId="sidebar-wrapper"/>
</div>
```

---

## Fase 3 — Sidebar & Navbar

### Icon Mapping: Bootstrap Icons → Phosphor Icons

| Dipakai di | `bi bi-*` | `ph-*` |
|---|---|---|
| Sidebar | `bi-grid-fill` | `ph-fill ph-squares-four` |
| Sidebar | `bi-cash-coin` | `ph-fill ph-cash-register` |
| Sidebar | `bi-receipt-cutoff` | `ph-fill ph-receipt` |
| Sidebar | `bi-journal-richtext` | `ph-fill ph-book-open` |
| Sidebar | `bi-cart-plus` | `ph-fill ph-shopping-cart-simple` |
| Sidebar | `bi-wallet2` | `ph-fill ph-wallet` |
| Sidebar | `bi-shop` | `ph-fill ph-storefront` |
| Sidebar | `bi-people` | `ph-fill ph-users` |
| Sidebar | `bi-person-gear` | `ph-fill ph-user-gear` |
| Sidebar | `bi-stars` | `ph-fill ph-sparkle` |
| Sidebar | `bi-display` | `ph-fill ph-monitor` |
| Sidebar | `bi-box-seam` | `ph-fill ph-package` |
| Navbar | `bi-list` | `ph-bold ph-list` |
| Navbar | `bi-bell` | `ph-bold ph-bell` |
| Order | `bi-question-circle` | `ph-bold ph-question` |
| Toast | `bi-check-circle-fill` | `ph-fill ph-check-circle` |
| Toast | `bi-x-circle-fill` | `ph-fill ph-x-circle` |
| Toast | `bi-exclamation-triangle-fill` | `ph-fill ph-warning` |
| Toast | `bi-info-circle-fill` | `ph-fill ph-info` |

### Navbar: Bootstrap Dropdown → Alpine Dropdown

```html
<!-- SEBELUM -->
<li class="nav-item dropdown">
  <a data-bs-toggle="dropdown" ...>
  <ul class="dropdown-menu dropdown-menu-end">

<!-- SESUDAH -->
<div x-data="{ open: false }" class="relative">
  <button @click="open=!open" @click.outside="open=false" ...>
  <div x-show="open" x-transition
       class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border ...">
```

---

## Fase 5 — POS: Pola Migrasi Modal

Semua `modal fade` Bootstrap → Alpine modal pattern:

```html
<!-- SEBELUM: Bootstrap Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Judul</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">...</div>
      <div class="modal-footer">...</div>
    </div>
  </div>
</div>

<!-- SESUDAH: Alpine Modal -->
<div x-show="showMyModal"
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
     @keydown.escape.window="showMyModal=false">
  <div x-transition
       class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl">
    <div class="flex items-center justify-between p-5 border-b dark:border-slate-700">
      <h5 class="text-lg font-bold">Judul</h5>
      <button @click="showMyModal=false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
        <i class="ph-bold ph-x text-xl"></i>
      </button>
    </div>
    <div class="p-5">...</div>
    <div class="flex justify-end gap-3 p-5 border-t dark:border-slate-700">...</div>
  </div>
</div>
```

---

## Fase 8 — Cleanup

```bash
# Hapus Bootstrap dari package.json
npm remove bootstrap bootstrap-icons

# Hapus sass
npm remove sass

# Rebuild
npm run build

# Verifikasi tidak ada sisa Bootstrap
grep -r "bi bi-\|data-bs-\|bootstrap\.\|offcanvas\|modal fade\|btn btn-\|d-flex\|d-none\|col-md-\|fw-bold" \
  resources/views/pages/tenant resources/views/layouts resources/views/components \
  --include="*.blade.php" --include="*.php" \
  | grep -v "store/resto\|store/retail"
```

---

## Checklist Final

- [ ] `npm run build` sukses tanpa error
- [ ] Sidebar desktop muncul dan bisa di-toggle
- [ ] Sidebar mobile bisa dibuka/tutup (Alpine drawer)
- [ ] Dark mode toggle berfungsi
- [ ] Navbar dropdown notifikasi berfungsi (Alpine)
- [ ] Dashboard page: semua stat cards & charts tampil benar
- [ ] Order list: tabel dan filter berfungsi
- [ ] Modal split bill bisa dibuka dan berfungsi
- [ ] Kasir (POS): semua modal bisa dibuka, cart berfungsi
- [ ] Halaman Product, Setting, Wallet tampil benar
- [ ] Tidak ada `bi bi-*` tersisa di dashboard files
- [ ] Tidak ada `data-bs-*` tersisa
- [ ] Tidak ada `bootstrap.Modal` / `bootstrap.Offcanvas` tersisa
