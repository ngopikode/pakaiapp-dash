# Alur Bisnis (Business Logic) - Dapur (Kitchen)

Berikut adalah *flowchart* murni dari sisi **alur bisnis operasional dapur**, yang menggambarkan bagaimana Koki (Chef) dan Pelayan (Waiter) berinteraksi dengan pesanan yang masuk.

## 1. Flowchart Operasional Dapur

```mermaid
flowchart TD
    %% Styling
    classDef dapur fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef sistem fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    classDef kasir fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef selesai fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef warning fill:#ffebee,stroke:#c62828,stroke-width:2px;

    MulaiDapur([Koki Memantau Layar Dapur / KDS]) --> PantauLayar
    
    PantauLayar["📺 Layar Menampilkan Tiket Antrean Pesanan<br>(Dikelompokkan per Meja/Invoice)"]:::sistem --> CekBahan
    
    CekBahan{"Apakah Bahan Tersedia?"}
    
    %% Kasus Bahan Habis
    CekBahan -- "Tidak (Bahan Habis/Rusak)" --> TolakPesanan
    TolakPesanan["🗣️ Koki Memberi Tahu Kasir/Pelayan"]:::dapur --> KasirVoid
    KasirVoid["❌ Kasir Melakukan Pembatalan (Void)<br>dan Menawarkan Menu Pengganti ke Pelanggan"]:::kasir --> LayarUpdate
    LayarUpdate["📺 Tiket Otomatis Hilang/Berubah dari Layar Dapur"]:::sistem
    
    %% Kasus Normal
    CekBahan -- "Ya (Bahan Siap)" --> MulaiMasak
    MulaiMasak["🧑‍🍳 Koki Menekan Tombol 'Mulai Dimasak'"]:::dapur --> StatusProses
    
    StatusProses["🔥 Status Berubah: Sedang Diproses<br>(Kasir & Pelayan tahu makanan sedang dibuat)"]:::sistem --> MasakSelesai
    
    MasakSelesai["🧑‍🍳 Makanan Matang, Koki Menekan 'Selesai'"]:::dapur --> StatusSiap
    
    StatusSiap["🍲 Status Berubah: Siap Disajikan<br>(Notifikasi/Tanda untuk Pelayan)"]:::sistem --> PanggilPelayan
    
    PanggilPelayan["🛎️ Koki Meletakkan Makanan di Pick-up Counter<br>dan Membunyikan Bel/Memanggil Pelayan"]:::dapur --> PelayanAmbil
    
    PelayanAmbil["🚶 Pelayan Mengambil Makanan<br>dan Mengantarkannya ke Meja Pelanggan"]:::sistem --> CekTiket
    
    CekTiket{"Apakah Semua Makanan di Tiket Ini Sudah Selesai?"}
    
    CekTiket -- "Belum (Masih ada yang antre)" --> PantauLayar
    CekTiket -- "Ya (Semua Matang)" --> TiketSelesai
    
    TiketSelesai["✅ Tiket Pesanan Menghilang dari Layar Dapur"]:::selesai
```

## Analisa Logika Bisnis Dapur
1. **Pengelompokan (Batching)**: Dapur yang sibuk tidak memasak per *invoice*, melainkan per *item* atau *batch*. Layar Dapur (*Kitchen Display System*) harus mampu memisahkan makanan yang baru masuk ("Menunggu") dengan makanan yang sudah masuk wajan ("Sedang Diproses").
2. **Komunikasi Dua Arah**: Koki butuh fitur "Tolak/Bahan Habis". Jika di tengah proses memasak kokinya sadar bahan bakunya rusak, harus ada mekanisme koki menekan tombol "Habis" agar kasir langsung mendapat notifikasi untuk membatalkan item tersebut (Void). 
3. **Pemisahan Dapur (Station)**: Jika restoran cukup besar, pesanan minuman harusnya masuk ke layar Barista, sedangkan makanan masuk ke Koki. Saat ini sistem Anda menggabungkan semuanya dalam satu layar `kitchen`.

---

## 2. Alur Teknis (Code Logic) - Modul Dapur
*Bagaimana kode `kitchen.php` merespons tindakan dari Koki di atas.*

```mermaid
flowchart TD
    %% Styling
    classDef method fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef db fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef loop fill:#fff8e1,stroke:#fbc02d,stroke-width:2px,stroke-dasharray: 5 5;
    classDef warning fill:#ffebee,stroke:#c62828,stroke-width:2px,color:#c62828;

    LoadDapur["UI: Layar Dapur di-Refresh"] --> QueryDB
    
    QueryDB["DB: Order::with('items')<br>Where status IN ('paid', 'progress')<br>OR (status='pending' AND is_online=false)"]:::db --> RenderUI
    
    RenderUI["UI Menampilkan Batch Item<br>(waiting vs processing)"] --> ActionChef
    
    ActionChef{"Tindakan Koki"}
    
    %% Koki Klik Mulai Dimasak
    ActionChef -- "Klik 'Mulai Dimasak'" --> M1["Method: markAsProcessing()"]:::method
    M1 --> DB_Item_Process["DB: OrderItem->kitchen_status = 'processing'"]:::db
    DB_Item_Process --> M2["Method: recalculateOrderStatus()"]:::method
    
    M2 --> DB_Order_Process["DB: Order->kitchen_status = 'processing'<br>Order->status = 'progress'"]:::db
    DB_Order_Process --> RenderUI
    
    %% Koki Klik Selesai
    ActionChef -- "Klik 'Selesai'" --> M3["Method: markAsReady()"]:::method
    M3 --> DB_Item_Ready["DB: OrderItem->kitchen_status = 'ready'"]:::db
    DB_Item_Ready --> M4["Method: recalculateOrderStatus()"]:::method
    
    M4 --> CekSisa{"Ada Item yang<br>Masih 'waiting' / 'processing'?"}
    
    CekSisa -- "Ya" --> Loop_Order_Prog["DB: Order->kitchen_status menyesuaikan item terendah"]:::loop
    Loop_Order_Prog --> RenderUI
    
    CekSisa -- "Tidak (Semua Ready)" --> DB_Order_Ready["DB: Order->kitchen_status = 'ready'"]:::db
    
    DB_Order_Ready --> CekLunas{"amount_paid >= total_price?"}
    
    CekLunas -- "Belum Lunas (Open Bill)" --> ProgressLagi["DB: Order->status = 'progress'"]:::db
    CekLunas -- "Sudah Lunas" --> Completed["DB: Order->status = 'completed'"]:::db
    
    ProgressLagi --> RenderUI
    Completed --> OrderHilang["Order Tidak Tampil Lagi di Dapur"]
```

## Temuan / Bug Teknis pada Logika Kode Dapur
1. **Tidak Ada Fitur Tolak/Kosong (*Out of Stock*)**: Koki tidak memiliki fungsi (*method*) di dalam `kitchen.php` untuk menolak pesanan jika bahan habis.
2. **Bug `markAsProcessing` Menarik Semua Item Sekaligus**: Berdasarkan fungsi `$order->items()->where('kitchen_status', 'waiting')->update(...)`, jika ada 5 item 'waiting' dalam satu invoice, mengklik "Mulai Dimasak" akan **mengubah ke-5 item tersebut menjadi 'processing' secara massal**. Koki tidak bisa memilih untuk memasak 1 per 1 itemnya.
3. **Bug Multi-Kasir (Is Online)**: Kode memfilter antrean dapur dengan klausa `where('status', 'pending')->where('is_online', false)`. Ini baik karena menahan order *online* yang belum dibayar. Namun, jika pelayan (*waiter*) menggunakan perangkat *mobile* dan aplikasi menganggapnya sebagai *online*, ordernya tidak akan masuk ke dapur sampai pelanggan bayar!
