<?php

namespace App\Tenant\Services;

use App\Tenant\Data\ShiftClosingData;
use App\Tenant\Data\ShiftExpenseData;
use App\Tenant\Models\Core\Shift;
use App\Tenant\Models\Core\ShiftExpense;
use App\Tenant\Models\Core\StockOpname;
use App\Tenant\Models\Core\Wallet;
use App\Tenant\Models\Resto\RawMaterial;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ShiftService
{
    protected ?TenantWalletService $walletService = null;

    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    /**
     * @throws Throwable
     */
    public function openShift(int $userId, float $startingCash): Shift
    {
        $existingShift = Shift::where('user_id', $userId)
            ->where('status', Shift::STATUS_ACTIVE)
            ->exists();

        if ($existingShift) throw new Exception(message: 'Kasir sudah memiliki shift yang aktif.');

        try {
            DB::beginTransaction();

            $shift = Shift::create([
                'user_id' => $userId,
                'started_at' => now(),
                'starting_cash' => $startingCash,
                'status' => Shift::STATUS_ACTIVE,
            ]);

            if ($startingCash > 0) {
                $this->walletService()->deductBalance(
                    amount: $startingCash,
                    reference: $shift,
                    description: 'Modal awal laci kasir (Shift #' . $shift->id . ')',
                    walletType: Wallet::TYPE_CASH
                );
            }

            DB::commit();

            return $shift;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function addExpense(Shift $shift, ShiftExpenseData $data): ShiftExpense
    {
        if ($shift->status !== Shift::STATUS_ACTIVE) {
            throw new Exception(message: 'Shift sudah ditutup, tidak dapat menambahkan pengeluaran.');
        }

        if ($data->amount <= 0) {
            throw new Exception(message: 'Nominal pengeluaran harus lebih besar dari 0.');
        }

        try {
            DB::beginTransaction();

            $expense = $shift->expenses()->create([
                'amount' => $data->amount,
                'description' => $data->description,
            ]);

            $shift->increment('cash_expenses', $data->amount);

            DB::commit();

            return $expense;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return array<int, array>
     */
    public function initiateClose(Shift $shift): array
    {
        if ($shift->status !== Shift::STATUS_ACTIVE) {
            return [];
        }

        $criticalMaterials = RawMaterial::where('is_critical', true)->select(['id', 'name', 'unit', 'stock'])->get();

        return $criticalMaterials->map(fn (RawMaterial $material) => [
            'id' => $material->id,
            'name' => $material->name,
            'unit' => $material->unit,
            'system_stock' => (float)$material->stock,
            'physical_stock' => null,
            'note' => null,
        ])->toArray();
    }

    /**
     * @throws Throwable
     */
    public function closeShift(Shift $shift, ShiftClosingData $data): Shift
    {
        if ($shift->status !== Shift::STATUS_ACTIVE) {
            throw new Exception(message: 'Shift sudah ditutup.');
        }

        try {
            DB::beginTransaction();

            $lockedShift = Shift::lockForUpdate()->find($shift->id);

            $expectedCash = $lockedShift->starting_cash + $lockedShift->cash_sales - $lockedShift->cash_expenses;
            $difference = $data->actualCash - $expectedCash;

            $opname = StockOpname::create([
                'shift_id' => $lockedShift->id,
                'user_id' => $lockedShift->user_id,
                'status' => StockOpname::STATUS_COMPLETED,
            ]);

            foreach ($data->opnameItems as $item) {
                $rawMaterial = RawMaterial::lockForUpdate()->find($item->rawMaterialId);
                if (!$rawMaterial) {
                    continue;
                }

                $stockDifference = $item->physicalStock - (float)$rawMaterial->stock;

                $opname->items()->create([
                    'opnameable_type' => RawMaterial::class,
                    'opnameable_id' => $rawMaterial->id,
                    'system_stock' => $rawMaterial->stock,
                    'physical_stock' => $item->physicalStock,
                    'difference' => $stockDifference,
                    'note' => $item->note,
                ]);

                if ($stockDifference < 0) {
                    $rawMaterial->decrement('stock', abs($stockDifference));
                } elseif ($stockDifference > 0) {
                    $rawMaterial->increment('stock', $stockDifference);
                }
            }

            $lockedShift->update([
                'status' => Shift::STATUS_CLOSED,
                'ended_at' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $data->actualCash,
                'difference' => $difference,
            ]);

            $this->walletService()->addBalance(
                amount: $data->actualCash,
                reference: $lockedShift,
                description: 'Setoran tutup shift kasir tanggal ' . now()->format('d/m/Y H:i'),
                walletType: Wallet::TYPE_CASH
            );

            DB::commit();

            return $lockedShift;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
