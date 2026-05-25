<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    private TenantWalletService $walletService;

    public function __construct(TenantWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Charge the transaction fee according to the Pay-As-You-Go capping logic.
     */
    public function chargeTransactionFee(Order $order): void
    {
        // 1. Transaction value must be >= Rp 1.000 to count towards capping or fee.
        if ($order->total_price < 1000) {
            return;
        }

        DB::transaction(function () use ($order) {
            $wallet = $this->walletService->getWallet();
            $wallet = DB::table('wallets')->where('id', $wallet->id)->lockForUpdate()->first();
            
            $currentMonth = date('Y-m');

            $monthlyTransactionCount = $wallet->monthly_transaction_count;
            $monthlyFeePaid = (float) $wallet->monthly_fee_paid;
            $monthlyVoidCount = $wallet->monthly_void_count;

            // Reset counters if billing period changed
            if ($wallet->current_billing_period !== $currentMonth) {
                $monthlyTransactionCount = 0;
                $monthlyFeePaid = 0.0;
                $monthlyVoidCount = 0;
                
                DB::table('wallets')->where('id', $wallet->id)->update([
                    'current_billing_period' => $currentMonth,
                    'monthly_transaction_count' => 0,
                    'monthly_fee_paid' => 0,
                    'monthly_void_count' => 0,
                ]);
            }

            $currentTenant = tenant();
            $settingService = app(\App\Services\SettingService::class);
            
            $trxFee = $settingService->get('trx_fee', $currentTenant, 300);
            $fupLimit = $settingService->get('fup_limit', $currentTenant, 5000);
            $cappingLimit = $settingService->get('capping_limit', $currentTenant, 150000);

            $feeToCharge = $trxFee;

            // FUP limit check
            if ($monthlyTransactionCount >= $fupLimit) {
                $feeToCharge = $trxFee; // Charge small fee again after FUP
            } else if ($monthlyFeePaid >= $cappingLimit) {
                // Capping hit! Free transaction.
                $feeToCharge = 0;
            }

            if ($feeToCharge > 0) {
                $this->walletService->deductBalance(
                    $feeToCharge,
                    $order,
                    "Biaya layanan pakaiapp untuk pesanan {$order->invoice_code}"
                );
                $monthlyFeePaid += $feeToCharge;
            }

            $monthlyTransactionCount++;

            // Update the stats
            DB::table('wallets')->where('id', $wallet->id)->update([
                'monthly_transaction_count' => $monthlyTransactionCount,
                'monthly_fee_paid' => $monthlyFeePaid,
            ]);

            Log::info("[BillingService] Charged fee for order {$order->invoice_code}", [
                'fee_charged' => $feeToCharge,
                'monthly_fee_paid' => $monthlyFeePaid,
                'monthly_tx_count' => $monthlyTransactionCount,
            ]);
        });
    }

    /**
     * Process penalty for excessive voided orders.
     */
    public function processVoidPenalty(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $wallet = $this->walletService->getWallet();
            $wallet = DB::table('wallets')->where('id', $wallet->id)->lockForUpdate()->first();

            $currentMonth = date('Y-m');
            $monthlyTransactionCount = $wallet->monthly_transaction_count;
            $monthlyFeePaid = (float) $wallet->monthly_fee_paid;
            $monthlyVoidCount = $wallet->monthly_void_count;

            if ($wallet->current_billing_period !== $currentMonth) {
                // Ignore penalty if it's the first void of the month
                DB::table('wallets')->where('id', $wallet->id)->update([
                    'current_billing_period' => $currentMonth,
                    'monthly_transaction_count' => 0,
                    'monthly_fee_paid' => 0,
                    'monthly_void_count' => 1,
                ]);
                return;
            }

            $monthlyVoidCount++;
            
            $currentTenant = tenant();
            $settingService = app(\App\Services\SettingService::class);
            
            $voidAllowance = $settingService->get('void_allowance_percentage', $currentTenant, 0.05);
            $minFreeVoids = $settingService->get('min_free_voids', $currentTenant, 10);
            $voidPenaltyFee = $settingService->get('void_penalty_fee', $currentTenant, 300);

            // Allow % voids, minimum of X free voids
            $allowedVoids = max($minFreeVoids, $monthlyTransactionCount * $voidAllowance);

            if ($monthlyVoidCount > $allowedVoids) {
                $this->walletService->deductBalance(
                    $voidPenaltyFee,
                    $order,
                    "Penalti void berlebih untuk pesanan {$order->invoice_code}"
                );
                
                Log::info("[BillingService] Charged penalty for voided order {$order->invoice_code}", [
                    'void_count' => $monthlyVoidCount,
                    'allowed' => $allowedVoids
                ]);
            }

            DB::table('wallets')->where('id', $wallet->id)->update([
                'monthly_void_count' => $monthlyVoidCount,
            ]);
        });
    }
}
