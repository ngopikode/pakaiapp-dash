<?php

namespace App\Livewire;

use App\Models\Quota;
use App\Services\SettingService;
use App\Services\TenantWalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title("Beli Slot")]
class extends Component {
    // Konfigurasi Harga & Jumlah Slot
    public int $additionalSlots = 50;
    public int $price = 10000; // Rp 10.000

    public function buySlot(): void
    {
        try {
            DB::transaction(function () {
                // 1. Ambil data kuota saat ini, lock agar aman dari race condition
                $quota = Quota::where('type', 'PRODUCT_SLOT')->lockForUpdate()->first();

                if (!$quota) {
                    throw new Exception("Data kuota tidak ditemukan.");
                }

                // 2. Potong Saldo Pakaiapp
                // Jika saldo kurang, ini otomatis throw Exception dan transaksi DIBATALKAN (Rollback)
                app(TenantWalletService::class)->deductBalance(
                    $this->price,
                    $quota, // Gunakan model Quota sebagai referensi Ledger transaksi
                    "Pembelian otomatis penambahan $this->additionalSlots slot produk"
                );

                // 3. Jika saldo sukses dipotong, tambahkan total slotnya
                $quota->increment('total_slots', $this->additionalSlots);
            });

            // Beri notifikasi sukses
            $this->dispatch('notify', message: "Mantap! Slot produk berhasil ditambah $this->additionalSlots. Silakan upload menu baru Anda!", type: 'success');

            // Opsional: Jika kamu punya komponen list produk, trigger refresh
            $this->dispatch('product-slot-updated');

        } catch (Exception $e) {
            // Tampilkan error (misal: "Kredit tidak mencukupi...")
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function with(): array
    {
        return [
            'quota' => Quota::firstOrCreate(
                ['type' => 'PRODUCT_SLOT'],
                [
                    'total_slots' => app(SettingService::class)->get('product_slots', tenant(), 12),
                    'used_slots' => 0
                ]
            ),
            'walletBalance' => app(TenantWalletService::class)->getWallet()->balance
        ];
    }
};
