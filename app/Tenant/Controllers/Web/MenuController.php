<?php

namespace App\Tenant\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\SettingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    protected ?SettingService $settingService = null;

    protected function settingService(): SettingService
    {
        return $this->settingService ??= app(SettingService::class);
    }

    public function show(Product $product): View|Factory|\Illuminate\View\View|RedirectResponse
    {
        $setting = StoreSetting::cached();

        // Redirect jika store tidak active atau sedang tutup
        if (!$setting || !$setting->is_active) return redirect()->route('index');

        if ($setting->operating_hours && !$setting->isOpenNow()) return redirect()->route('index');

        $product->load(['variants', 'extras']);

        $orderTypes = [];

        $waNumber = preg_replace('/\D/', '', $setting->whatsapp_number ?: '6281234567890');
        if (str_starts_with($waNumber, '0')) $waNumber = '62' . substr($waNumber, 1);
        if ($setting->is_dinein_active) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];
        if ($setting->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        if ($setting->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes)) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Takeaway'];

        // --- SEO & META OPTIMIZATION ---
        $storeName = $setting->name ?? 'Menu Digital';
        $themeColor = $setting->theme_color ?? '#f59e0b';
        $canonicalUrl = url()->current();

        $pageTitle = "$product->name di $storeName";

        $hooks = ['Cuma', 'Hanya', 'Spesial', 'Nikmati seharga', 'Dapatkan cuma', 'Pesan sekarang'];
        $randomHook = $hooks[array_rand($hooks)];
        $priceString = 'Rp ' . number_format($product->price, 0, ',', '.');
        $rawDesc = $product->description ? trim($product->description) : "Menu favorit dari $storeName.";

        $fullDesc = "$randomHook $priceString! $rawDesc";
        $ogDesc = Str::limit($fullDesc, 155);
        $imageVersion = $product->updated_at ? $product->updated_at->timestamp : time();

        $appFeeAmount = $this->settingService()->get('default_trx_fee', tenant(), 300);

        // Preorder & WA Config
        $isWaCheckoutActive = $setting->is_wa_checkout_active ?? false;
        $isPreorderActive = $setting->is_preorder_active ?? false;
        $preorderConfig = [];
        if ($isPreorderActive) {
            $preorderConfig = [
                'earliest_date' => \Carbon\Carbon::now()->timezone('Asia/Jakarta')->addDays((int)($setting->preorder_min_days ?? 1))->format('Y-m-d'),
                'zones' => \App\Tenant\Models\Core\DeliveryZone::where('is_active', true)->get(['id', 'name', 'shipping_cost']),
            ];
        }

        return view('pages.tenant.store.resto.product', [
            'product' => $product,
            'productData' => $product->toFrontendArray(),
            'setting' => $setting,
            'waNumber' => $waNumber,
            'orderTypes' => $orderTypes,
            'storeName' => $storeName,
            'themeColor' => $themeColor,
            'canonicalUrl' => $canonicalUrl,
            'pageTitle' => $pageTitle,
            'ogDesc' => $ogDesc,
            'imageVersion' => $imageVersion,
            'appFeeAmount' => $appFeeAmount,
            'isWaCheckoutActive' => $isWaCheckoutActive,
            'isPreorderActive' => $isPreorderActive,
            'preorderConfig' => $preorderConfig,
        ]);
    }

    public function shareAsStory(Product $product): View|Factory|\Illuminate\View\View
    {
        $restaurant = StoreSetting::cached() ?? new StoreSetting(['name' => 'Resto']);
        $productUrl = route('menu.show', $product);

        return view('pages.tenant.store.story-preview', [
            'restaurant' => $restaurant,
            'product' => $product,
            'image_url' => $product->image ? Storage::url($product->image) : null,
            'product_url' => $productUrl,
            'share_text' => $this->generateShareText($product, $restaurant, $productUrl),
            'share_title' => "$product->name - $restaurant->name",
        ]);
    }

    private function generateShareText(Product $product, StoreSetting $restaurant, string $url): string
    {
        $generalHooks = [
            '✨ Barangkali lagi kepikiran',
            '🌟 Salah satu menu favorit dari',
            '📍 Jangan sampai kelewat nikmatnya',
            '🍃 Pilihan pas buat nemenin harimu:',
            '🤍 Rekomendasi spesial untukmu:',
            '💡 Wajib cobain menu andalan ini:',
        ];

        $randomHook = $generalHooks[array_rand($generalHooks)];
        $priceFormatted = 'Rp' . number_format($product->price, 0, ',', '.');

        $shareText = "$randomHook $product->name di $restaurant->name. ";

        if (!empty($product->description)) {
            $shortDesc = Str::limit($product->description, 50);
            $shareText .= "($shortDesc) ";
        }

        $shareText .= "Harganya cuma $priceFormatted aja. Order praktis di sini: $url";

        return $shareText;
    }
}
