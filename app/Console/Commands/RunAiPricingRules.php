<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use App\Tenant\Models\Ai\AiPricingRule;
use App\Tenant\Models\Core\ProductVariant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class RunAiPricingRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pakaiapp:run-ai-pricing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate and execute dynamic AI pricing rules across all active tenants';

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): void
    {
        $this->info('Starting AI Pricing Evaluation...');

        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $this->info("Evaluating for tenant: $tenant->id");

            // 1. Ambil semua rule yang aktif
            $rules = AiPricingRule::where('is_active', true)
                ->with(['productVariants'])
                ->get();

            $now = Carbon::now('Asia/Jakarta');
            $currentDay = $now->format('D'); // Mon, Tue, dll
            $currentTime = $now->format('H:i:s');

            // Kita kumpulkan ID variant yang sedang dikontrol diskonnya saat ini,
            // agar variant lain yang tidak terkena diskon bisa di-reset.
            $activeVariantIds = [];

            foreach ($rules as $rule) {
                $days = $rule->active_days ?? [];

                // Cek apakah hari ini dan jam ini sesuai dengan jadwal rule
                $isDayMatch = in_array($currentDay, $days);
                $isTimeMatch = ($currentTime >= $rule->start_time) && ($currentTime <= $rule->end_time);

                if ($isDayMatch && $isTimeMatch) {
                    $this->info("Rule [$rule->rule_name] is ACTIVE.");

                    foreach ($rule->productVariants as $variant) {
                        $activeVariantIds[] = $variant->id;

                        // Hitung harga diskon
                        $originalPrice = $variant->price;
                        $discountValue = $variant->pivot->discount_value;
                        $newPrice = $originalPrice;

                        if ($rule->rule_type === 'percentage') {
                            $newPrice = $originalPrice - ($originalPrice * ($discountValue / 100));
                        } elseif ($rule->rule_type === 'fixed_cut') {
                            $newPrice = $originalPrice - $discountValue;
                        }

                        // Pastikan harga tidak minus
                        if ($newPrice < 0) $newPrice = 0;

                        // Update variant dengan harga coret
                        $variant->update([
                            'active_discount_price' => $newPrice,
                            'active_discount_name' => $rule->rule_name,
                        ]);
                    }
                }
            }

            // 2. Reset semua variant yang dulunya punya diskon tapi sekarang tidak masuk dalam rule aktif
            ProductVariant::whereNotNull('active_discount_price')
                ->whereNotIn('id', $activeVariantIds)
                ->update([
                    'active_discount_price' => null,
                    'active_discount_name' => null,
                ]);

            tenancy()->end();
        }

        $this->info('AI Pricing Evaluation completed successfully.');
    }
}
