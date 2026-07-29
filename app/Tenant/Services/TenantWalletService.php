<?php

namespace App\Tenant\Services;

use App\Tenant\Models\Core\Wallet;
use App\Tenant\Models\Core\WalletTransaction;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class TenantWalletService
{
    private function defaultWalletName(string $type): string
    {
        return match ($type) {
            Wallet::TYPE_BILLING => 'Deposit Pakaiapp',
            Wallet::TYPE_CASH => 'Kas Tunai',
            Wallet::TYPE_BANK => 'Kas Bank',
            Wallet::TYPE_GATEWAY => 'Kas Gateway',
        };
    }

    /**
     * Mendapatkan atau membuat dompet tenant berdasarkan tipe.
     */
    public function getWallet(string $type = Wallet::TYPE_BILLING): Wallet
    {
        return Wallet::firstOrCreate(
            ['type' => $type],
            [
                'name' => $this->defaultWalletName($type),
                'balance' => 0,
            ]
        );
    }

    /**
     * Menambah saldo dompet (Top-up, Refund, dsb)
     *
     * @throws Throwable
     */
    public function addBalance(float|int $amount, Model $reference, ?string $description = null, string $walletType = Wallet::TYPE_BILLING): WalletTransaction
    {
        return $this->processTransaction('CREDIT', $amount, $reference, $description, $walletType);
    }

    /**
     * Memotong saldo dompet (Pembayaran transaksi, Beli slot, dsb)
     *
     * @throws Exception|Throwable Jika saldo tidak mencukupi
     */
    public function deductBalance(float|int $amount, Model $reference, ?string $description = null, string $walletType = Wallet::TYPE_BILLING): WalletTransaction
    {
        return $this->processTransaction('DEBIT', $amount, $reference, $description, $walletType);
    }

    /**
     * Core logic mutasi dompet dengan sistem penguncian mutlak (Pessimistic Locking).
     * Dibuat private agar controller hanya bisa memanggil addBalance / deductBalance.
     *
     * @throws Throwable
     */
    private function processTransaction(string $type, float|int $amount, Model $reference, ?string $description = null, string $walletType = Wallet::TYPE_BILLING): WalletTransaction
    {
        try {
            DB::beginTransaction();

            $expectedWalletId = $this->getWallet($walletType)->id;
            // Menggunakan lockForUpdate untuk mencegah Race Condition (Double Spend / Dirty Read)
            $wallet = Wallet::where('id', $expectedWalletId)->lockForUpdate()->firstOrFail();

            $openingBalance = (float) $wallet->balance;
            $transactionAmount = (float) $amount;

            if ($transactionAmount <= 0) {
                throw new Exception('Nominal transaksi harus lebih besar dari 0.');
            }

            if ($type === 'DEBIT') {
                if ($openingBalance < $transactionAmount) {
                    throw new Exception('Kredit tidak mencukupi. Sisa saldo: Rp' . number_format($openingBalance, 0, ',', '.'));
                }
                $closingBalance = $openingBalance - $transactionAmount;
            } else {
                $closingBalance = $openingBalance + $transactionAmount;
            }

            // Update saldo akhir di tabel wallets
            $wallet->balance = $closingBalance;
            $wallet->save();

            // Catat riwayat ledger/mutasi mutlak ke tabel wallet_transactions
            $transaction = $wallet->transactions()->create([
                'type' => $type,
                'amount' => $transactionAmount,
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'reference_id' => $reference->getKey(),
                'reference_type' => $reference->getMorphClass(),
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $transaction;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
