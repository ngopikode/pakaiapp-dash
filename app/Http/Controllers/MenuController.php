<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class MenuController extends Controller
{
    /**
     * Show a product preview for social media bots.
     */
    public function showProductPreview(Request $request, string $productId)
    {
        $restaurant = StoreSetting::first() ?? new StoreSetting(['name' => 'Resto']);
        $product = $this->getProduct($productId);
        
        $fullReactUrl = url('/');

        if ($this->isSocialMediaBot($request)) {
            return view('tenant.product_preview', [
                'restaurant' => $restaurant,
                'product' => $product,
                'image_url' => $product->image ? Storage::url($product->image) : null,
                'react_app_url' => $fullReactUrl,
            ]);
        }

        return redirect("$fullReactUrl#$productId");
    }

    public function shareAsStory(Request $request, $productId)
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

    public function shareToStory(Request $request, $productId)
    {
        $restaurant = StoreSetting::first() ?? new StoreSetting(['name' => 'Resto']);
        $product = $this->getProduct($productId);

        $productUrl = url("/menu/$productId?t=" . time());

        $storyText = $this->generateShareText($product, $restaurant, $productUrl);
        $encodedText = urlencode($storyText);

        return redirect()->away("https://wa.me/?text=$encodedText");
    }

    public function generateStoryImage(Request $request, $productId)
    {
        set_time_limit(60);

        $restaurant = StoreSetting::first() ?? new StoreSetting(['name' => 'Resto']);
        $product = $this->getProduct($productId);
        $subdomain = tenant('id');

        // --- CACHING STRATEGY ---
        $cacheFileName = "stories/$subdomain/{$product->id}_{$product->updated_at->timestamp}.jpg";
        $downloadName = Str::slug($product->name) . '-story.jpg';

        if (Storage::disk('public')->exists($cacheFileName)) {
            return $this->downloadStoryImage($cacheFileName, $downloadName);
        }

        $this->clearOldStoryCache($subdomain, $product->id);

        $productImagePath = Storage::disk('public')->path($product->image);

        if (!$product->image || !file_exists($productImagePath)) {
            abort(404, 'Product image not found');
        }

        $this->createStoryImage($product, $restaurant, $productImagePath, $cacheFileName);

        return $this->downloadStoryImage($cacheFileName, $downloadName);
    }

    private function isSocialMediaBot(Request $request): bool
    {
        $userAgent = strtolower((string) $request->header('User-Agent'));
        $bots = ['whatsapp', 'facebookexternalhit', 'facebot', 'twitterbot', 'linkedinbot'];

        foreach ($bots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true;
            }
        }
        return false;
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
            $shortDesc = \Illuminate\Support\Str::limit($product->description, 50, '...');
            $shareText .= "($shortDesc) ";
        }

        $shareText .= "Harganya cuma $priceFormatted aja. Order praktis di sini: $url";

        return $shareText;
    }

    private function clearOldStoryCache(string $subdomain, int $productId): void
    {
        $directory = "stories/$subdomain";
        if (!Storage::disk('public')->exists($directory)) {
            return;
        }

        $oldFiles = Storage::disk('public')->files($directory);
        foreach ($oldFiles as $file) {
            if (Str::startsWith(basename($file), "{$productId}_")) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    private function downloadStoryImage(string $filePath, string $downloadName)
    {
        return response()->download(Storage::disk('public')->path($filePath), $downloadName, [
            'Content-Type' => 'image/jpeg'
        ]);
    }

    private function createStoryImage(Product $product, StoreSetting $restaurant, string $sourceImagePath, string $destCachePath): void
    {
        $canvasWidth = 720;
        $canvasHeight = 1280;

        $img = Image::create($canvasWidth, $canvasHeight)->fill('#ffffff');

        $backgroundImage = Image::read($sourceImagePath);
        $backgroundImage->cover(36, 64);
        $backgroundImage->resize($canvasWidth, $canvasHeight);

        $overlay = Image::create($canvasWidth, $canvasHeight)->fill('rgba(0, 0, 0, 0.35)');
        $backgroundImage->place($overlay);

        $img->place($backgroundImage);

        $mainImage = Image::read($sourceImagePath);
        $mainImage->scale(width: 550);
        $img->place($mainImage, 'center');

        $fontBold = $this->getFontPath('Poppins-Bold.ttf');
        $fontRegular = $this->getFontPath('Poppins-Regular.ttf');
        $fontLight = $this->getFontPath('Poppins-Light.ttf');

        if ($fontBold) {
            $img->text($product->name, $canvasWidth / 2, 900, function (FontFactory $font) use ($fontBold) {
                $font->filename($fontBold);
                $font->size(50);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        if ($fontRegular) {
            $img->text('Rp ' . number_format($product->price, 0, ',', '.'), $canvasWidth / 2, 980, function (FontFactory $font) use ($fontRegular) {
                $font->filename($fontRegular);
                $font->size(40);
                $font->color('#ffdd00');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        if ($fontRegular) {
            $img->text('Cek menu lengkap di link bio!', $canvasWidth / 2, 1150, function (FontFactory $font) use ($fontRegular) {
                $font->filename($fontRegular);
                $font->size(24);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
        }

        if ($fontLight) {
            $img->text($restaurant->name, $canvasWidth / 2, 1230, function (FontFactory $font) use ($fontLight) {
                $font->filename($fontLight);
                $font->size(24);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        $directory = dirname($destCachePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $img->save(Storage::disk('public')->path($destCachePath), 80);
    }

    private function getFontPath(string $fontName): ?string
    {
        $path = public_path("fonts/$fontName");
        return file_exists($path) ? $path : null;
    }
}
