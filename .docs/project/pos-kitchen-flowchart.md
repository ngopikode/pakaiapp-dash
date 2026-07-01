# Alur Bisnis (Business Logic) - Dapur (Kitchen)

Berikut adalah *flowchart* murni dari sisi **alur bisnis operasional dapur** yang telah diperbarui dengan kapabilitas pemrosesan per-item dan validasi anti-fraud.

## 1. Flowchart Operasional Dapur

```mermaid
flowchart TD
    %% Styling
    classDef dapur fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef sistem fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    classDef kasir fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef selesai fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;

    MulaiDapur([Koki Memantau Layar Dapur / KDS]) --> PantauLayar
    
    PantauLayar["📺 Layar Menampilkan Tiket Antrean Pesanan<br>(Item terpisah: Menunggu vs Diproses)"]:::sistem --> CekBahan
    
    CekBahan{"Koki Siap Memasak?"}
    
    %% Kasus Kasir Membatalkan Pesanan
    CekBahan -- "Kasir Membatalkan (Void)" --> OrderDibatalkan
    OrderDibatalkan["❌ Pesanan Dibatalkan Kasir sebelum Koki Bertindak"]:::kasir --> LayarUpdate2
    LayarUpdate2["📺 Tiket Langsung Lenyap dari Layar Dapur"]:::sistem
    
    %% Kasus Normal (Memasak Per-Item)
    CekBahan -- "Pilih 1 Item / Semua" --> MulaiMasak
    MulaiMasak["🧑‍🍳 Koki Menekan 'Masak' (Satuan) atau 'Masak Semua'"]:::dapur --> ValidasiBatal
    
    ValidasiBatal{"Validasi Sistem:<br>Apakah Order sudah Dibatalkan?"}
    ValidasiBatal -- "Ya (Dibatalkan)" --> TolakAksi
    TolakAksi["🛑 Sistem Menolak Aksi Koki<br>Memunculkan Alert 'Pesanan Dibatalkan'"]:::sistem
    
    ValidasiBatal -- "Tidak (Valid)" --> StatusProses
    StatusProses["🔥 Item Berubah Status: Sedang Diproses<br>(Tiket terpecah otomatis jika hanya 1 item dimasak)"]:::sistem --> MasakSelesai
    
    MasakSelesai["🧑‍🍳 Item Matang, Koki Menekan 'Selesai' (Satuan/Massal)"]:::dapur --> StatusSiap
    
    StatusSiap["🍲 Item Berubah Status: Siap Disajikan"]:::sistem --> CekTiket
    
    CekTiket{"Apakah Semua Item di Tiket Ini Sudah Siap?"}
    
    CekTiket -- "Belum" --> PantauLayar
    CekTiket -- "Ya (Semua Matang)" --> TiketSelesai
    
    TiketSelesai["✅ Tiket Menghilang dari Layar Dapur (Completed)"]:::selesai
```

## Analisa Logika Bisnis Dapur Terkini
1. **Pemrosesan Fleksibel (Satuan vs Massal)**: Koki sekarang memiliki otonomi penuh. Jika ada 5 menu dalam 1 nota, koki bisa memasak 1 menu dulu, dan tiket di layar akan otomatis memecah diri (1 sedang diproses, 4 menunggu). Tombol aksi massal ("Mulai Masak Semua") juga tetap tersedia untuk kecepatan.
2. **Keamanan Anti-Fraud & Race-Condition**: Ada *shield* pertahanan baru. Jika Kasir secara kebetulan menekan tombol Batal tepat beberapa detik sebelum Koki menekan tombol Masak, sistem KDS akan langsung menolak input Koki. Ini mencegah sinkronisasi status hantu (*ghost orders*).
3. **Live Refresh Tanpa Gangguan**: Koki dilengkapi dengan tombol Refresh Livewire yang memperbarui daftar pesanan masuk tanpa membuat browser berkedip (*full reload*).

---

## 2. Alur Teknis (Code Logic) - Modul Dapur Terkini

```mermaid
flowchart TD
    %% Styling
    classDef method fill:#e3f2fd,stroke:#1565c0,stroke-width:2px;
    classDef db fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;

    QueryDB["DB: Load Order & Items<br>Status: Pending/Progress/Paid"]:::db --> RenderUI
    
    RenderUI["UI Livewire Render"] --> ActionChef
    
    ActionChef{"Tindakan Koki"}
    
    %% Satuan
    ActionChef -- "Klik 'Masak' (1 Item)" --> M1["markItemAsProcessing(itemId)"]:::method
    M1 --> CekValid1{"Order Status != cancelled?"}
    CekValid1 -- "Valid" --> DB_Item_Process["Update OrderItem->kitchen_status = processing"]:::db
    
    %% Massal
    ActionChef -- "Klik 'Masak Semua'" --> M2["markAsProcessing(orderId)"]:::method
    M2 --> CekValid2{"Order Status != cancelled?"}
    CekValid2 -- "Valid" --> DB_All_Process["Update All Waiting Items -> processing"]:::db
    
    DB_Item_Process --> Recalculate
    DB_All_Process --> Recalculate
    
    Recalculate["recalculateOrderStatus(orderId)"]:::method --> CalcLogic
    
    CalcLogic{"Kalkulasi:"}
    CalcLogic -- "Ada waiting & processing" --> SetProcessing["Order->kitchen_status = processing"]:::db
    CalcLogic -- "Semua ready" --> SetReady["Order->kitchen_status = ready"]:::db
    
    SetReady --> CekLunas{"amount_paid >= total_price?"}
    CekLunas -- "Belum Lunas" --> SetProgress["Order->status = progress"]:::db
    CekLunas -- "Sudah Lunas/Free" --> SetCompleted["Order->status = completed"]:::db
```
