# Analisa Alur Sistem Kasir Resto

Dokumen ini berisi pemetaan lengkap sistem kasir restoran Anda, terbagi menjadi dua bagian: **Alur Bisnis (Operasional)** dan **Alur Teknis (Code & Database)**.

---

## 1. Alur Bisnis (Business Logic)
*Sudut pandang kasir dan operasional di lapangan.*

```mermaid
flowchart TD
    %% Styling
    classDef kasir fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef sistem fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    classDef dapur fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef bayar fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef selesai fill:#ffebee,stroke:#c62828,stroke-width:2px;
    classDef loop fill:#fff8e1,stroke:#fbc02d,stroke-width:2px,stroke-dasharray: 5 5;
    classDef split fill:#e1bee7,stroke:#8e24aa,stroke-width:2px;

    Mulai([Kasir Membuka Halaman POS]) --> InputData
    
    InputData["👤 Kasir Memasukkan:<br>1. Nama Pelanggan<br>2. Nomor Meja<br>3. Jenis Order"]:::kasir --> PilihMenu
    
    PilihMenu["🍔 Kasir Memilih Menu"]:::kasir --> Hitung
    
    Hitung["💻 Sistem Menghitung otomatis:<br>Subtotal + Pajak + Service Charge"]:::sistem --> KeputusanCheckout
    
    KeputusanCheckout{"Pilih Metode Checkout"}
    
    %% --- SKENARIO A: BUKA BILL / DINE-IN (OPEN BILL) ---
    KeputusanCheckout -- "Simpan Pesanan / Buka Bill" --> DapurDineIn
    
    DapurDineIn["🍳 Pesanan Muncul di Layar Dapur"]:::dapur --> Makan
    Makan["🍽️ Pelanggan Menikmati Makanan"]:::sistem --> KeputusanPelanggan
    
    KeputusanPelanggan{"Keinginan Pelanggan"}
    
    %% 1. Siklus Tambah Pesanan (Loop)
    KeputusanPelanggan -- "Ingin Nambah Makanan" --> PanggilKasir
    PanggilKasir["🙋‍♂️ Memanggil Kasir"]:::kasir --> EditBill
    EditBill["📝 Kasir Membuka Kembali Bill (Edit Order)<br>dan Menambahkan Menu Baru"]:::loop --> DapurDineInTambahan
    DapurDineInTambahan["🍳 Item TAMBAHAN Muncul di Dapur"]:::dapur --> Makan
    
    %% 2. Minta Bill Akhir
    KeputusanPelanggan -- "Selesai & Minta Bill" --> BukaTagihan
    BukaTagihan["📄 Kasir Membuka Tagihan Akhir"]:::kasir --> CekDiskon
    
    CekDiskon{"Ada Diskon/Promo?"}
    CekDiskon -- "Ya" --> TerapkanDiskon
    TerapkanDiskon["🏷️ Kasir Menginput Diskon"]:::kasir --> ModeBayar
    CekDiskon -- "Tidak" --> ModeBayar
    
    ModeBayar{"Metode Pelunasan Tagihan?"}
    
    ModeBayar -- "Bayar Penuh (1 Orang)" --> ProsesBayarAkhir
    ProsesBayarAkhir["💰 Kasir Memproses Pembayaran"]:::bayar --> LunasA
    
    ModeBayar -- "Split Bill (Pisah Tagihan)" --> JenisSplit
    JenisSplit{"Pilih Cara Split Bill"}:::split
    
    JenisSplit -- "Split by Nominal / Bagi Rata" --> SplitNominal
    SplitNominal["💳 Kasir Memproses Pembayaran Sebagian<br>(Partial Payment)"]:::split --> CekSisaTagihan
    
    JenisSplit -- "Split by Item (Pisah Menu)" --> SplitItem
    SplitItem["📋 Kasir Memecah Tagihan Berdasarkan Menu<br>(Membuat Sub-struk)"]:::split --> CekSisaTagihan
    
    CekSisaTagihan{"Masih Ada Sisa Tagihan?"}
    CekSisaTagihan -- "Ya" --> ModeBayar
    CekSisaTagihan -- "Tidak (Saldo Rp 0)" --> LunasA
    
    LunasA["✅ Transaksi Selesai & Lunas"]:::selesai
    
    %% --- SKENARIO B: BAYAR LANGSUNG / TAKEAWAY ---
    KeputusanCheckout -- "Bayar Langsung" --> CekDiskonAwal
    
    CekDiskonAwal{"Ada Diskon/Promo?"}
    CekDiskonAwal -- "Ya" --> TerapkanDiskonAwal
    TerapkanDiskonAwal["🏷️ Kasir Menginput Diskon"]:::kasir --> ProsesBayarAwal
    CekDiskonAwal -- "Tidak" --> ProsesBayarAwal
    
    ProsesBayarAwal["💰 Kasir Memproses Pembayaran"]:::bayar --> DapurTakeaway
    
    DapurTakeaway["🍳 Pesanan Muncul di Layar Dapur"]:::dapur --> TungguMakanan
    TungguMakanan["⏳ Pelanggan Menunggu Makanan Siap"]:::sistem --> AmbilMakanan
    
    AmbilMakanan["🛍️ Pelanggan Mengambil Pesanan"]:::sistem --> LunasB
    LunasB["✅ Transaksi Selesai & Lunas"]:::selesai
```

---

## 2. Alur Teknis (Code & Database Logic)
*Bagaimana kode Laravel (`resto-cashier.php`), Livewire, dan Database memproses alur bisnis di atas.*

```mermaid
flowchart TD
    %% Styling
    classDef method fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef db fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef svc fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef check fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    classDef warning fill:#ffebee,stroke:#c62828,stroke-width:2px,color:#c62828;

    UI[UI: Livewire Submit Cart] --> Branch{Pilih Action Method}

    %% ==========================================
    %% JALUR A: createOrder() -> OPEN BILL
    %% ==========================================
    Branch -- "Simpan Antrean" --> M1["Method: createOrder()"]:::method
    
    M1 --> CekEdit{Apakah 'addToOrder' aktif?}
    
    %% Jika Tambah Pesanan
    CekEdit -- "Ya (Tambah Pesanan)" --> M1_A["Update Order Exists:<br/>subtotal += new_subtotal<br/>kitchen_status = 'waiting'"]:::db
    
    %% Jika Buat Baru
    CekEdit -- "Tidak (Buat Baru)" --> M1_B["Order::create()<br/>status = 'pending'<br/>kitchen_status = 'waiting'"]:::db
    
    M1_A --> DB_Items1
    M1_B --> DB_Items1
    
    DB_Items1["OrderItem::create()<br/>kitchen_status = 'waiting'"]:::db --> DB_Stock1
    
    DB_Stock1["ProductVariant->decrement('stock')<br/>RawMaterial->decrement('stock')"]:::db --> SelesaiA["Response: Success<br/>(Belum potong Wallet Tenant!)"]

    %% Pelunasan Open Bill
    UIPay[UI: Modal Pelunasan] --> M3["Method: processPayment()"]:::method
    M3 --> DB_Lock["Order::lockForUpdate()->find()"]:::db
    DB_Lock --> DB_PayUpdate["Order->update()<br/>payment_method, discount, amount_paid"]:::db
    
    DB_PayUpdate --> CheckPaid{"amount_paid >= total_price?"}:::check
    
    CheckPaid -- "False (Cicil/Split Nominal)" --> SplitDB["Order->status = 'progress'"]:::db
    SplitDB --> UIPay
    
    CheckPaid -- "True (Lunas)" --> StatusLunas{"Status Dapur (kitchen_status)?"}:::check
    StatusLunas -- "'ready' / 'completed'" --> DB_Compl["Order->status = 'completed'"]:::db
    StatusLunas -- "'waiting' / 'processing'" --> DB_Paid["Order->status = 'paid'"]:::db
    
    DB_Compl --> SVC1
    DB_Paid --> SVC1
    
    SVC1["BillingService::chargeTransactionFee()<br/>(Potong Saldo Wallet Tenant)"]:::svc --> SelesaiA2["Response: Payment Success"]

    %% ==========================================
    %% JALUR B: processDirectCheckout() -> BAYAR LANGSUNG
    %% ==========================================
    Branch -- "Bayar Langsung" --> M4["Method: processDirectCheckout()"]:::method
    M4 --> DB_CreateDirect["Order::create()<br/>status = 'paid'<br/>kitchen_status = 'waiting'<br/>amount_paid = total_price"]:::db
    DB_CreateDirect --> DB_Items2["OrderItem::create()<br/>kitchen_status = 'waiting'"]:::db
    DB_Items2 --> DB_Stock2["ProductVariant->decrement('stock')<br/>RawMaterial->decrement('stock')"]:::db
    DB_Stock2 --> SVC2["BillingService::chargeTransactionFee()"]:::svc
    SVC2 --> SelesaiB["Response: Checkout Success"]
```

## Analisa Logika Kode Saat Ini (Code Flaws & Bugs)

Pemetaan kode di atas mengungkapkan beberapa kelemahan pada implementasi teknis saat ini:

### 🔴 1. Bug Dapur Ter-Reset (Double Cook Bug)
- **Titik Lemah**: Di `createOrder()` (jalur *Tambah Pesanan*), saat kasir mengedit pesanan yang sudah ada, *parent* tabel `Order` langsung dipaksa diperbarui menjadi `kitchen_status = 'waiting'` (terlihat di kotak `M1_A`). 
- **Dampak**: Jika pesanan pelanggan gelombang pertama sedang digoreng (`processing`) oleh koki, namun pelanggan menambah pesanan es teh manis, koki akan kebingungan karena di layar rekap pesanan utama statusnya kembali menjadi *waiting*.
- **Solusi**: Biarkan `kitchen_status` pada tabel `Order` tetap pada status *progress* tertingginya, dan biarkan modul Dapur mengacu murni pada `kitchen_status` yang ada di tabel *child* (`OrderItem`).

### 🟡 2. Void Menu & Pengembalian Stok (Restock) Tidak Ada
- **Titik Lemah**: Terlihat pada kotak eksekusi `ProductVariant->decrement('stock')` di kedua metode. Pemotongan stok **langsung terjadi seketika** di sisi *database*.
- **Dampak**: Secara kode, sistem Anda murni melakukan pengurangan (*decrement*). Tidak ada *method* atau *endpoint* untuk `increment('stock')`. Jika pesanan pelanggan dibatalkan atau diretur, stok barang dan bahan baku mentah dapur yang sudah terpotong tidak akan pernah bisa kembali, mengacaukan *inventory* restoran.

### 🟡 3. Split by Nominal Berjalan Tanpa Disadari
- **Titik Lemah**: Pada kotak pengecekan `amount_paid >= total_price?`, sistem menetapkan status `'progress'` jika pembayaran kurang dari total.
- **Dampak Positif**: *Backend* Anda secara teknis **sudah mendukung pembayaran parsial/cicilan (Split by Nominal)** secara tidak sengaja.
- **Kekurangan**: UI di Livewire kasir Anda belum mendukung hal ini secara sadar. UI hanya mengirimkan nominal penuh (seringkali melempar sisa sebagai kembalian). Perlu ada modifikasi UI untuk mengizinkan kasir menginput "Bayar Sebagian".
