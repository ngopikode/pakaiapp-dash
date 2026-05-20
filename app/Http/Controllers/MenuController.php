<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function shareAsStory($productId)
    {
        $restaurant = StoreSetting::first() ?? new StoreSetting(['name' => 'Resto']);
        $product = $this->getProduct($productId);

        $fullReactUrl = url('/');
        $productUrl = "$fullReactUrl/menu/$productId";

        return view('tenant.story_preview', [
            'restaurant' => $restaurant,
            'product' => $product,
            'image_url' => $product->image ? Storage::url($product->image) : null,
            'product_url' => $productUrl,
            'share_text' => $this->generateShareText($product, $restaurant, $productUrl),
            'share_title' => "$product->name - $restaurant->name"
        ]);
    }


    private function getProduct(string $productId): Product
    {
        $id = str_contains($productId, 'product-') ? explode('-', $productId)[1] : $productId;
        return Product::findOrFail($id);
    }

    private function generateShareText(Product $product, StoreSetting $restaurant, string $url): string
    {
        $generalHooks = [
            '✨ Barangkali lagi kepikiran',
            '🌟 Salah satu menu favorit dari',
            '📍 Jangan sampai kelewat nikmatnya',
            '🍃 Pilihan pas buat nemenin harimu:',
            '🤍 Rekomendasi spesial untukmu:',
            '💡 Wajib cobain menu andalan ini:'
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
