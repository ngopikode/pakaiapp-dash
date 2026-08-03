<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

/**
 * Kontrak data untuk OrderService::processOrder().
 * Semua caller (Livewire POS, API, Retail) harus menggunakan DTO ini.
 * Field optional menggunakan null sebagai default — service akan fallback ke StoreSetting jika null.
 */
class ProcessOrderData extends Data
{
    public function __construct(
        public string $customerName = 'Pelanggan Umum',
        public string $orderType = 'takeaway',
        public string $paymentMethod = 'cash',
        public string $status = 'pending',
        public ?string $tableNumber = null,
        public ?string $notes = null,
        public ?string $invoiceCode = null,
        public ?string $customerPhone = null,
        public ?string $customerEmail = null,
        public ?string $duitkuPaymentMethod = null,
        public ?int $userId = null,
        public float $globalDiscount = 0,
        public float $amountPaid = 0,
        public float $changeAmount = 0,
        public ?bool $isTaxActive = null,
        public ?bool $isServiceActive = null,
        public ?bool $isApplicationFeePassed = null,
        public bool $isOnline = false,
    ) {}
}
