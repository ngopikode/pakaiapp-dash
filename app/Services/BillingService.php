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

            $feeToCharge = 300;

            // FUP limit check (5000)
            if ($monthlyTransactionCount >= 5000) {
                $feeToCharge = 300; // Charge small fee again after FUP
            } else if ($monthlyFeePaid >= 150000) {
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
            
            // Allow 5% voids, minimum of 10 free voids
            $allowedVoids = max(10, $monthlyTransactionCount * 0.05);

            if ($monthlyVoidCount > $allowedVoids) {
                $this->walletService->deductBalance(
                    300,
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
