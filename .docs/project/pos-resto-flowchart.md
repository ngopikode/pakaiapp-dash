# Analisa Alur Sistem Kasir Resto Terkini

Dokumen ini berisi pemetaan lengkap sistem kasir restoran setelah perombakan skalabilitas dan keamanan tingkat *Enterprise*.

---

## 1. Alur Bisnis (Business Logic) Terkini
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
    classDef adv fill:#e1bee7,stroke:#8e24aa,stroke-width:2px;

    Mulai([Kasir Membuka Halaman POS]) --> InputData
    InputData["👤 Kasir Memasukkan:<br>1. Nama Pelanggan<br>2. Nomor Meja<br>3. Jenis Order"]:::kasir --> PilihMenu
    PilihMenu["🍔 Kasir Memilih Menu"]:::kasir --> Hitung
    Hitung["💻 Sistem Menghitung otomatis:<br>Subtotal + PB1 + Service Charge"]:::sistem --> KeputusanCheckout
    
    KeputusanCheckout{"Pilih Metode Checkout"}
    
    %% --- SKENARIO BUKA BILL (OPEN BILL) ---
    KeputusanCheckout -- "Simpan Pesanan / Buka Bill" --> DapurDineIn
    
    DapurDineIn["🍳 Item Pesanan Muncul di Layar Dapur (Waiting)"]:::dapur --> Makan
    Makan["🍽️ Pelanggan Menikmati Makanan"]:::sistem --> KeputusanPelanggan
    
    KeputusanPelanggan{"Tindakan di Tengah Jalan"}
    
    %% 1. Siklus Tambah Pesanan (Loop)
    KeputusanPelanggan -- "Tambah Menu" --> EditBill
    EditBill["📝 Kasir Tambah Menu (Asal nota belum Lunas/Batal)"]:::loop --> DapurDineInTambahan
    DapurDineInTambahan["🍳 Item TAMBAHAN Saja yang Muncul di Dapur"]:::dapur --> Makan
    
    %% 2. Void Item (Batal Menu)
    KeputusanPelanggan -- "Batal Menu Tertentu" --> CekVoid
    CekVoid{"Apakah Koki Sudah Masak?"}
    CekVoid -- "Belum (Waiting)" --> VoidAman
    VoidAman["🗑️ Kasir Void Item & Stok Otomatis Kembali"]:::kasir --> Makan
    CekVoid -- "Sudah (Processing/Ready)" --> VoidDitolak
    VoidDitolak["🛑 Sistem Menolak Void (Anti-Fraud)"]:::sistem --> Makan
    
    %% 3. Pisah/Gabung Meja
    KeputusanPelanggan -- "Urusan Tagihan Lanjutan" --> UrusanStruk
    UrusanStruk{"Pilih Aksi Tagihan"}:::adv
    
    UrusanStruk -- "Split Bill (Pisah Nota)" --> SplitItem
    SplitItem["✂️ Kasir Memecah Menu ke Sub-Nota Baru"]:::adv --> MintaBill
    
    UrusanStruk -- "Gabung Struk (Merge Bill)" --> MergeBill
    MergeBill["🔗 Nota B Disedot ke Nota A (Nota B Hilang)"]:::adv --> MintaBill
    
    UrusanStruk -- "Selesai Makan" --> MintaBill
    
    %% Pelunasan
    MintaBill["📄 Kasir Membuka Tagihan Akhir"]:::kasir --> ModeBayar
    ModeBayar{"Metode Pelunasan?"}
    
    ModeBayar -- "Bayar Penuh" --> LunasA
    ModeBayar -- "Partial Payment (Cicil Nominal)" --> Cicil
    
    Cicil["💳 Kasir Menginput Pembayaran Sebagian<br>(Tombol Tetap Aktif)"]:::bayar --> CekSisaTagihan
    CekSisaTagihan{"Masih Ada Sisa Tagihan?"}
    CekSisaTagihan -- "Ya (Status Progress)" --> ModeBayar
    CekSisaTagihan -- "Tidak (Lunas)" --> LunasA
    
    LunasA["✅ Transaksi Selesai & Lunas"]:::selesai
```

---

## 2. Alur Teknis (Code & Database Logic) Terkini

```mermaid
flowchart TD
    %% Styling
    classDef method fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef db fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef check fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    classDef security fill:#ffebee,stroke:#c62828,stroke-width:2px,color:#c62828;

    UI[UI: Livewire Aksi Kasir] --> Branch{Pilih Action Method}

    %% ==========================================
    %% JALUR VOID & KEAMANAN
    %% ==========================================
    Branch -- "Hapus / Void Item" --> M_Void["voidItem(itemId)"]:::method
    M_Void --> CekFraud1{"Apakah item->kitchen_status<br>sedang processing/ready?"}:::security
    CekFraud1 -- "Ya (Bahaya)" --> Reject1["Tolak Aksi Kasir!"]:::security
    CekFraud1 -- "Tidak (Aman)" --> RestoreStock["Item Dihapus &<br>ProductVariant->increment()<br>RawMaterial->increment()"]:::db
    
    %% ==========================================
    %% JALUR CREATE / TAMBAH MENU
    %% ==========================================
    Branch -- "Simpan / Tambah Menu" --> M1["createOrder()"]:::method
    M1 --> CekEdit{Apakah addToOrder aktif?}
    
    CekEdit -- "Ya (Tambah Pesanan)" --> CekFraud2{"Apakah Order sudah Lunas/Batal?"}:::security
    CekFraud2 -- "Ya (Bahaya)" --> Reject2["Tolak Aksi Kasir!"]:::security
    CekFraud2 -- "Aman" --> M1_A["Update Subtotal & PB1 Order<br/>(TANPA me-reset kitchen_status Induk)"]:::db
    
    CekEdit -- "Tidak (Buat Baru)" --> M1_B["Order::create()"]:::db
    
    M1_A --> DB_Items
    M1_B --> DB_Items
    DB_Items["OrderItem::create() -> waiting<br/>ProductVariant->decrement()"]:::db --> SelesaiCreate["Success"]

    %% ==========================================
    %% JALUR PELUNASAN / PARTIAL
    %% ==========================================
    Branch -- "Proses Pembayaran" --> M_Pay["processPayment()"]:::method
    M_Pay --> Accumulate["Hitung akumulasi amount_paid"]:::method
    Accumulate --> CheckPaid{"amount_paid >= total_price?"}:::check
    CheckPaid -- "False (Partial)" --> StatusProgress["Order->status = 'progress'"]:::db
    CheckPaid -- "True (Lunas)" --> StatusLunas["Order->status = 'completed' / 'paid'"]:::db
```

## Solusi Inovatif yang Telah Diterapkan
1. **Keamanan Lintas Tab (Anti-Fraud Resto)**
   - Kasir tidak bisa mem-void item yang diam-diam sudah dimasak koki.
   - Kasir tidak bisa menambah pesanan ke meja yang ternyata sudah dibayar lunas melalui ponsel (*Digital Payment*) atau oleh kasir rekanannya.
2. **Fleksibilitas Pelunasan (Partial & Split/Merge)**
   - Mendukung pembayaran sebagian (*Partial Payment*). Tombol bayar tidak lagi diblokir jika uang pelanggan kurang, sistem otomatis mencatatnya sebagai cicilan (status `progress`).
   - Fitur Gunting (*Split Bill*) dan Fitur Panah (*Merge Bill*) saling melengkapi untuk mengizinkan kasir "Bongkar-Pasang" isi nota kapanpun dibutuhkan sebelum pelunasan akhir.
3. **Pengembalian Stok Akurat (Smart Restock)**
   - Fitur pembatalan (*Void/Cancel*) kini mendeteksi tipe toko. Pada Kasir Resto, sistem akan menelusuri resi resep (`recipes`) dan mengembalikan persis bahan mentah (`RawMaterial`) ke dalam gudang, membebaskan manajer inventaris dari audit manual.
