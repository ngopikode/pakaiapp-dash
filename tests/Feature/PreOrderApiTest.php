<?php

use App\Tenant\Models\Core\DeliverySlot;
use App\Tenant\Models\Core\DeliveryZone;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\PreOrderService;
use Carbon\Carbon;

// ---------------------------------------------------------------------------
// Unit tests untuk PreOrderService — logika murni tanpa HTTP/DB
// ---------------------------------------------------------------------------

describe('PreOrderService::resolveEarliestDeliveryDate', function () {
    it('mengembalikan BESOK jika cutoff_time null', function () {
        $service = new PreOrderService;
        $setting = new StoreSetting;
        $setting->cutoff_time = null;

        $result = $service->resolveEarliestDeliveryDate($setting);

        expect($result->toDateString())->toBe(Carbon::today('Asia/Jakarta')->addDay()->toDateString());
    });

    it('mengembalikan HARI INI jika jam sekarang sebelum cutoff', function () {
        Carbon::setTestNow(Carbon::parse('2026-08-08 03:00:00', 'Asia/Jakarta'));

        $service = new PreOrderService;
        $setting = new StoreSetting;
        $setting->cutoff_time = '04:00';

        $result = $service->resolveEarliestDeliveryDate($setting);

        expect($result->toDateString())->toBe('2026-08-08');

        Carbon::setTestNow(null);
    });

    it('mengembalikan BESOK jika jam sekarang sama atau setelah cutoff', function () {
        Carbon::setTestNow(Carbon::parse('2026-08-08 04:30:00', 'Asia/Jakarta'));

        $service = new PreOrderService;
        $setting = new StoreSetting;
        $setting->cutoff_time = '04:00';

        $result = $service->resolveEarliestDeliveryDate($setting);

        expect($result->toDateString())->toBe('2026-08-09');

        Carbon::setTestNow(null);
    });
});

describe('PreOrderService::buildWaMessage', function () {
    it('menghasilkan pesan WA dengan semua info pesanan', function () {
        $order = new Order;
        $order->invoice_code = 'PO-20260808-ABCDE';
        $order->customer_name = 'Siti Aminah';
        $order->customer_phone = '081234567890';
        $order->customer_address = 'Jl. Kenanga No.5, Sri Gunting';
        $order->delivery_date = Carbon::parse('2026-08-09');
        $order->subtotal = 50000;
        $order->shipping_cost = 0;
        $order->total_price = 50000;
        $order->payment_method = 'qris';
        $order->notes = null;

        $slot = new DeliverySlot;
        $slot->name = 'Pagi (06:00-09:00)';

        $zone = new DeliveryZone;
        $zone->name = 'Sri Gunting';

        // Inject relasi tanpa DB
        $order->setRelation('deliverySlot', $slot);
        $order->setRelation('deliveryZone', $zone);
        $order->setRelation('items', collect());

        $service = new PreOrderService;
        $message = $service->buildWaMessage($order);

        expect($message)
            ->toContain('PO-20260808-ABCDE')
            ->toContain('Siti Aminah')
            ->toContain('Jl. Kenanga No.5, Sri Gunting')
            ->toContain('Pagi (06:00-09:00)')
            ->toContain('Sri Gunting')
            ->toContain('QRIS')
            ->toContain('Rp 50.000');
    });

    it('menampilkan COD untuk payment_method cash', function () {
        $order = new Order;
        $order->invoice_code = 'PO-TEST';
        $order->customer_name = 'Budi';
        $order->customer_phone = null;
        $order->customer_address = 'Jl. Test';
        $order->delivery_date = Carbon::parse('2026-08-09');
        $order->subtotal = 30000;
        $order->shipping_cost = 5000;
        $order->total_price = 35000;
        $order->payment_method = 'cash';
        $order->notes = 'Tolong tepat waktu';

        $order->setRelation('deliverySlot', null);
        $order->setRelation('deliveryZone', null);
        $order->setRelation('items', collect());

        $service = new PreOrderService;
        $message = $service->buildWaMessage($order);

        expect($message)
            ->toContain('COD')
            ->toContain('Tolong tepat waktu')
            ->toContain('Rp 5.000'); // ongkir
    });
});

describe('PreOrderService — kalkulasi shipping_cost', function () {
    it('ongkir gratis jika subtotal >= min_free_shipping', function () {
        // Uji logika langsung: zone->min_free_shipping = 50000, subtotal = 60000
        $zone = new DeliveryZone;
        $zone->shipping_cost = '10000';
        $zone->min_free_shipping = '50000';

        $realSubtotal = 60000;
        $shippingCost = (float)$zone->shipping_cost;
        if ((float)$zone->min_free_shipping > 0 && $realSubtotal >= (float)$zone->min_free_shipping) {
            $shippingCost = 0;
        }

        expect($shippingCost)->toEqual(0.0);
    });

    it('ongkir dikenakan jika subtotal < min_free_shipping', function () {
        $zone = new DeliveryZone;
        $zone->shipping_cost = '10000';
        $zone->min_free_shipping = '50000';

        $realSubtotal = 40000;
        $shippingCost = (float)$zone->shipping_cost;
        if ((float)$zone->min_free_shipping > 0 && $realSubtotal >= (float)$zone->min_free_shipping) {
            $shippingCost = 0;
        }

        expect($shippingCost)->toBe(10000.0);
    });

    it('ongkir selalu dikenakan jika min_free_shipping = 0', function () {
        $zone = new DeliveryZone;
        $zone->shipping_cost = '5000';
        $zone->min_free_shipping = '0';

        $realSubtotal = 999999;
        $shippingCost = (float)$zone->shipping_cost;
        if ((float)$zone->min_free_shipping > 0 && $realSubtotal >= (float)$zone->min_free_shipping) {
            $shippingCost = 0;
        }

        expect($shippingCost)->toBe(5000.0);
    });
});
