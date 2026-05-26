<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class TenantWalletService
{
    /**
     * Mendapatkan atau membuat dompet utama tenant (Singleton per tenant database).
     */
    public function getWallet(): Wallet
    {
        return Wallet::firstOrCreate(
            ['id' => 1],
            ['balance' => 0]
        );
    }

    /**
     * Menambah saldo dompet (Top-up, Refund, dsb)
     * @param float|int $amount
     * @param Model $reference
     * @param string|null $description
     * @return WalletTransaction
     * @throws Throwable
     */
    public function addBalance(float|int $amount, Model $reference, ?string $description = null): WalletTransaction
    {
        return $this->processTransaction('CREDIT', $amount, $reference, $description);
    }

    /**
     * Memotong saldo dompet (Pembayaran transaksi, Beli slot, dsb)
     *
     * @throws Exception|Throwable Jika saldo tidak mencukupi
     */
    public function deductBalance(float|int $amount, Model $reference, ?string $description = null): WalletTransaction
    {
        return $this->processTransaction('DEBIT', $amount, $reference, $description);
    }

    /**
     * Core logic mutasi dompet dengan sistem penguncian mutlak (Pessimistic Locking).
     * Dibuat private agar controller hanya bisa memanggil addBalance / deductBalance.
     * @param string $type
     * @param float|int $amount
     * @param Model $reference
     * @param string|null $description
     * @return WalletTransaction
     * @throws Throwable
     */
    private function processTransaction(string $type, float|int $amount, Model $reference, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($type, $amount, $reference, $description) {
            $walletId = $this->getWallet()->id;

            // Menggunakan lockForUpdate untuk mencegah Race Condition (Double Spend / Dirty Read)
            $wallet = Wallet::where('id', $walletId)->lockForUpdate()->firstOrFail();

            $openingBalance = (float)$wallet->balance;
            $transactionAmount = (float)$amount;

            if ($transactionAmount <= 0) {
                throw new Exception("Nominal transaksi harus lebih besar dari 0.");
            }

            if ($type === 'DEBIT') {
                if ($openingBalance < $transactionAmount) {
                    throw new Exception("Kredit tidak mencukupi. Sisa saldo: Rp" . number_format($openingBalance, 0, ',', '.'));
                }
                $closingBalance = $openingBalance - $transactionAmount;
            } else {
                $closingBalance = $openingBalance + $transactionAmount;
            }

            // Update saldo akhir di tabel wallets
            $wallet->balance = $closingBalance;
            $wallet->save();

            // Catat riwayat ledger/mutasi mutlak ke tabel wallet_transactions
            return $wallet->transactions()->create([
                'type' => $type,
                'amount' => $transactionAmount,
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'reference_id' => $reference->getKey(),
                'reference_type' => $reference->getMorphClass(),
                'description' => $description,
                'created_by' => auth()->id(),
            ]);
        });
    }
}
