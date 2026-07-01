<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Tenant\Models\Core\ProductVariant;

class Order extends Model
{
    protected $fillable = [
        'id',
        'invoice_code',
        'table_number',
        'notes',
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_type',
        'is_online',
        'payment_method',
        'subtotal',
        'tax_amount',
        'service_charge_amount',
        'tax_percentage',
        'service_charge_percentage',
        'discount',
        'total_price',
        'amount_paid',
        'change_amount',
        'status',
        'kitchen_status',
        'user_id',
        'cancellation_note',

        // Duitku Payment Gateway fields
        'duitku_reference',
        'duitku_payment_url',
        'duitku_va_number',
        'duitku_payment_method',

        // Midtrans Payment Gateway fields
        'midtrans_snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',

        'is_printed',

        'created_at',
        'updated_at',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke kasir yang login
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    /**
     * Get the formatted and user-friendly payment method name.
     */
    public function getFormattedPaymentMethodAttribute(): string
    {
        if ($this->duitku_payment_method) {
            $code = strtoupper($this->duitku_payment_method);
            $fallbackMethods = [
                'BC' => 'BCA Virtual Account',
                'M2' => 'Mandiri Virtual Account',
                'I1' => 'BNI Virtual Account',
                'BR' => 'BRI Virtual Account',
                'BT' => 'Permata Virtual Account',
                'B1' => 'CIMB Niaga Virtual Account',
                'VA' => 'Maybank Virtual Account',
                'DN' => 'Danamon Virtual Account',
                'HN' => 'Hana Bank Virtual Account',
                'NC' => 'Neo Commerce Virtual Account',
                'SP' => 'ShopeePay',
                'DA' => 'DANA',
                'OV' => 'OVO',
                'LA' => 'LinkAja',
                'NQ' => 'QRIS (ShopeePay/DANA/OVO/LinkAja)',
            ];
            return $fallbackMethods[$code] ?? $this->duitku_payment_method;
        }

        return match (strtolower($this->payment_method ?? '')) {
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer Bank',
            default => $this->payment_method ?: '-',
        };
    }

    /**
     * Restore stock for all items in this order.
     * Used when an order is cancelled, failed, or underpaid.
     */
    public function restoreStock(): void
    {
        $this->load('items.variant.recipes.rawMaterial');
        foreach ($this->items as $item) {
            $variantIds = [];
            if (!empty($item->selected_variants)) {
                $variantIds = is_string($item->selected_variants) ? json_decode($item->selected_variants, true) : $item->selected_variants;
            } elseif ($item->variant_id) {
                $variantIds = [$item->variant_id];
            }

            if (!empty($variantIds)) {
                $variantsToRestore = ProductVariant::with('recipes.rawMaterial')->whereIn('id', $variantIds)->get();
                $storeType = tenant('store_type');
                
                foreach ($variantsToRestore as $variantToRestore) {
                    $variantToRestore->increment('stock', $item->quantity);

                    if ($storeType === 'resto') {
                        foreach ($variantToRestore->recipes as $recipe) {
                            if ($recipe->rawMaterial) {
                                $recipe->rawMaterial->increment('stock', $recipe->quantity_used * $item->quantity);
                            }
                        }
                    }
                }
            }
        }
    }
}
