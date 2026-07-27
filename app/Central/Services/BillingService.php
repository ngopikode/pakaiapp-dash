<?php

namespace App\Central\Services;

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\Wallet;
use App\Tenant\Services\SettingService;
use App\Tenant\Services\TenantWalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BillingService
{
    protected ?SettingService $settingService = null;

    public function __construct(protected readonly TenantWalletService $walletService) {}

    protected function settingService(): SettingService
    {
        return $this->settingService ??= app(SettingService::class);
    }

    /**
     * Locks the wallet for update and resets monthly counters if the billing period has changed.
     */
    private function lockAndPrepareWallet(): Wallet
    {
        // Optimization: lock immediately during the first fetch to avoid 2 separate queries
        $wallet = Wallet::lockForUpdate()->firstOrCreate(
            ['id' => 1],
            ['balance' => 0]
        );

        $currentMonth = date('Y-m');

        if (
            $wallet->current_billing_period !== $currentMonth
        ) $wallet->fill([
            'current_billing_period' => $currentMonth,
            'monthly_transaction_count' => 0,
            'monthly_fee_paid' => 0.0,
            'monthly_void_count' => 0,
        ]);

        return $wallet;
    }

    /**
     * Charge the transaction fee according to the Pay-As-You-Go capping logic.
     *
     * @throws Throwable
     */
    public function chargeTransactionFee(Order $order): void
    {
        $currentTenant = tenant();

        $settings = $this->settingService()->getMany([
            'min_trx_amount' => 1000,
            'trx_fee' => 300,
            'fup_limit' => 5000,
            'capping_limit' => 150000,
        ], $currentTenant);

        // Transaction value must be >= minimum threshold to count towards capping or fee.
        if ($order->total_price < $settings['min_trx_amount']) return;

        $trxFee = $settings['trx_fee'];
        $fupLimit = $settings['fup_limit'];
        $cappingLimit = $settings['capping_limit'];

        try {
            DB::beginTransaction();

            $wallet = $this->lockAndPrepareWallet();

            $isCapped = $wallet->monthly_fee_paid >= $cappingLimit;
            $isUnderFup = $wallet->monthly_transaction_count < $fupLimit;

            $feeToCharge = ($isCapped && $isUnderFup) ? 0 : $trxFee;

            $updateData = ['monthly_transaction_count' => $wallet->monthly_transaction_count + 1];

            if ($feeToCharge > 0) $updateData['monthly_fee_paid'] = $wallet->monthly_fee_paid + $feeToCharge;

            // Save counters FIRST before calling other services to prevent stale reads/deadlocks
            $wallet->update($updateData);

            if ($feeToCharge > 0) $this->walletService->deductBalance(
                amount: $feeToCharge,
                reference: $order,
                description: "Biaya layanan pakaiapp untuk pesanan $order->invoice_code"
            );

            Log::info("[BillingService] Charged fee for order $order->invoice_code", [
                'fee_charged' => $feeToCharge,
                'monthly_fee_paid' => $wallet->monthly_fee_paid,
                'monthly_tx_count' => $wallet->monthly_transaction_count,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process penalty for excessive voided orders.
     *
     * @throws Throwable
     */
    public function processVoidPenalty(Order $order): void
    {
        $currentTenant = tenant();

        $settings = $this->settingService()->getMany([
            'void_allowance_percentage' => 0.05,
            'min_free_voids' => 10,
            'void_penalty_fee' => 300,
        ], $currentTenant);

        $voidAllowance = $settings['void_allowance_percentage'];
        $minFreeVoids = $settings['min_free_voids'];
        $voidPenaltyFee = $settings['void_penalty_fee'];

        try {
            DB::beginTransaction();

            $wallet = $this->lockAndPrepareWallet();

            // Save counters FIRST before calling other services
            $wallet->update(['monthly_void_count' => $wallet->monthly_void_count + 1]);

            // Allow % voids, minimum of X free voids
            $allowedVoids = max($minFreeVoids, $wallet->monthly_transaction_count * $voidAllowance);

            if ($wallet->monthly_void_count > $allowedVoids) {
                if (
                    $voidPenaltyFee > 0
                ) $this->walletService->deductBalance(
                    amount: $voidPenaltyFee,
                    reference: $order,
                    description: "Penalti void berlebih untuk pesanan $order->invoice_code"
                );

                Log::info("[BillingService] Charged penalty for voided order $order->invoice_code", [
                    'void_count' => $wallet->monthly_void_count,
                    'allowed' => $allowedVoids,
                ]);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
