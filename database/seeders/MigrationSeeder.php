<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\StoreSetting;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MigrationSeeder extends Seeder
{
    public function run()
    {
        // === 1. Sama Roti Kukus ===
        $user1 = User::firstOrCreate(
            ['email' => 'owner@samarotikukus.com'],
            ['name' => 'Pemilik Rokus', 'password' => Hash::make('password')]
        );

        // Cek jika tenant sudah ada (berdasarkan domain)
        $tenantId1 = Str::slug('Sama Roti Kukus');
        $tenant1 = Tenant::find($tenantId1);
        if (!$tenant1) {
            $tenant1 = Tenant::create(['id' => $tenantId1]);
            $tenant1->domains()->create(['domain' => 'samarotikukus.pakaiapp.dep']);
        }

        $tenant1->run(function () use ($user1) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StoreSetting::truncate();
            Category::truncate();
            Product::truncate();
            \App\Models\ProductVariant::truncate();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            StoreSetting::create([
                'name' => 'Sama Roti Kukus',
                'logo' => 'logos/1/logo.png', // Sesuaikan
                'theme_color' => '#d4b982',
                'whatsapp_number' => '6282283668001',
                'address' => "Kompleks @allnewtsjcafe, Bangkot Kab Kampar",
                'is_active' => true,
                'hero_promo_text' => 'Promo',
                'hero_status_text' => 'Open until 11.00PM',
                'hero_headline' => 'Seruput & Gigit',
                'hero_tagline' => '"StayTune the Healthy People"',
                'hero_instagram_url' => 'https://www.instagram.com/sama.co.id',
                'navbar_brand_text' => 'Sama',
                'navbar_title' => 'Roti Kukus',
                'navbar_subtitle' => "Kampar • Est. 10'19th",
                'seo_title' => 'Sama Roti Kukus - Seruput & Gigit | Kuliner Hits Kampar',
                'seo_description' => 'Nikmati sensasi roti kukus lumer dan minuman kekinian paling hits di Kampar sejak 2019.',
                'seo_keywords' => 'Sama Roti Kukus, Rokus Kampar, Kuliner Kampar, Roti Kukus Lumer, Jajanan Kampar',
                'og_title' => 'Sama Roti Kukus - Seruput & Gigit',
                'og_description' => 'Camilan lumer paling hits di Kampar. Harga mulai 7k! Beli 4 Gratis 1.',
                'og_image' => 'og_images/1/og-main.jpg',
            ]);

            $catRoti = Category::create(['name' => 'Roti', 'slug' => Str::slug('Roti')]);
            $catMinuman = Category::create(['name' => 'Minuman', 'slug' => Str::slug('Minuman')]);

            $menuData = [
                ['cat' => 'roti', 'name' => 'Rokus Original', 'price' => 7000, 'desc' => 'Pilih 1 varian rasa klasik favoritmu.', 'options' => ['Cokelat Original', 'Tiramisu', 'Keju', 'Sarikaya', 'Choco Cruncy'], 'img' => 'products/1/rokus-ori.png'],
                ['cat' => 'roti', 'name' => 'Rokus Mix', 'price' => 8000, 'desc' => 'Perpaduan 2 rasa lumer dalam satu gigitan.', 'options' => ['Cokelat + Keju', 'Cokelat + Oreo', 'Cokelat + Kacang', 'Cokelat + Tiramisu', 'Sarikaya + Keju', 'Tiramisu + Keju', 'Tiramisu + Oreo', 'Choco Cruncy + Oreo'], 'img' => 'products/1/rokus-mix.png'],
                ['cat' => 'roti', 'name' => 'Rokus Combo', 'price' => 10000, 'desc' => 'Eksplorasi rasa dengan maksimal 3 topping.', 'options' => ['Cokelat', 'Tiramisu', 'Keju', 'Sarikaya', 'Oreo', 'Kacang', 'Choco Cruncy'], 'img' => 'products/1/rokus-combo.png'],
                ['cat' => 'minuman', 'name' => 'Kopi Susu Aren', 'price' => 11000, 'desc' => 'Signature coffee dengan gula aren asli.', 'options' => [], 'img' => 'products/1/kopi-susu-aren.jpeg'],
                ['cat' => 'minuman', 'name' => 'Blue Ocean', 'price' => 11000, 'desc' => 'Kesegaran soda biru lemon yang unik.', 'options' => [], 'img' => 'products/1/blue-ocean.jpeg'],
                ['cat' => 'minuman', 'name' => 'Milky Mango', 'price' => 11000, 'desc' => 'Creamy milk dengan rasa mangga manis.', 'options' => [], 'img' => 'products/1/milky-mango.jpeg'],
                ['cat' => 'minuman', 'name' => 'Passion Soda', 'price' => 11000, 'desc' => 'Soda markisa yang menyegarkan dahaga.', 'options' => [], 'img' => 'products/1/passion-soda.jpeg'],
                ['cat' => 'minuman', 'name' => 'Choco Milky', 'price' => 11000, 'desc' => 'Susu cokelat creamy yang nyoklat banget.', 'options' => [], 'img' => 'products/1/choco-milky.jpeg'],
                ['cat' => 'minuman', 'name' => 'Yakult Mango', 'price' => 11000, 'desc' => 'Perpaduan Yakult dan mangga yang segar.', 'options' => [], 'img' => 'products/1/yakult-mango.jpeg'],
                ['cat' => 'minuman', 'name' => 'Yakult Peach', 'price' => 11000, 'desc' => 'Kesegaran Yakult dengan aroma peach.', 'options' => [], 'img' => 'products/1/yakult-peach.jpeg'],
                ['cat' => 'minuman', 'name' => 'Cendol Aren', 'price' => 10000, 'desc' => 'Cita rasa tradisional cendol gula aren.', 'options' => [], 'img' => 'products/1/cendol-aren.png'],
            ];

            foreach ($menuData as $item) {
                $product = Product::create([
                    'category_id' => ($item['cat'] === 'roti') ? $catRoti->id : $catMinuman->id,
                    'name' => $item['name'],
                    'description' => $item['desc'],
                    'image' => $item['img'],
                    'has_variants' => !empty($item['options']),
                    'is_active' => true,
                ]);

                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionName) {
                        $product->variants()->create([
                            'name' => $optionName,
                            'price' => $item['price'],
                            'stock' => 100,
                        ]);
                    }
                } else {
                    $product->variants()->create([
                        'name' => 'Regular',
                        'price' => $item['price'],
                        'stock' => 100,
                    ]);
                }
            }
        });

        // === 2. Martabak Hening ===
        $user2 = User::firstOrCreate(
            ['email' => 'owner@martabakhening.com'],
            ['name' => 'Owner Martabak Hening', 'password' => Hash::make('password')]
        );

        $tenantId2 = Str::slug('Martabak Hening');
        $tenant2 = Tenant::find($tenantId2);
        if (!$tenant2) {
            $tenant2 = Tenant::create(['id' => $tenantId2]);
            $tenant2->domains()->create(['domain' => 'martabakhening.pakaiapp.dep']);
        }

        $tenant2->run(function () use ($user2) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StoreSetting::truncate();
            Category::truncate();
            Product::truncate();
            \App\Models\ProductVariant::truncate();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            StoreSetting::create([
                'name' => 'Martabak Hening',
                'logo' => 'logos/martabak-hening/logo.png',
                'theme_color' => '#f59e0b',
                'whatsapp_number' => '6285172441544',
                'address' => "Jl. Kemenangan No. 88, (Depan Indomaret Point)",
                'is_active' => true,
                'hero_promo_text' => 'Promo Spesial',
                'hero_status_text' => 'Buka 17.00 - 23.00',
                'hero_headline' => 'Manis & Gurih',
                'hero_tagline' => 'Nikmati kelembutan di setiap gigitan.',
                'hero_instagram_url' => 'https://www.instagram.com/martabakhening',
                'navbar_brand_text' => 'Martabak',
                'navbar_title' => 'Hening',
                'navbar_subtitle' => 'Sejak 2020',
                'seo_title' => 'Martabak Hening - Manis & Telur Premium',
                'seo_description' => 'Martabak manis dengan topping melimpah dan martabak telur renyah.',
                'seo_keywords' => 'Martabak Hening, Martabak Manis, Terang Bulan, Martabak Telur, Kuliner Malam',
                'og_title' => 'Martabak Hening - Tebal & Lembut',
                'og_description' => 'Lapar malam? Martabak Hening solusinya. Order via WhatsApp sekarang!',
                'og_image' => 'og_images/martabak-hening/og-main.jpg',
            ]);

            $catManis = Category::create(['name' => 'Martabak Manis', 'slug' => Str::slug('Martabak Manis')]);
            $catTelur = Category::create(['name' => 'Martabak Telur', 'slug' => Str::slug('Martabak Telur')]);
            $catMinum = Category::create(['name' => 'Minuman Segar', 'slug' => Str::slug('Minuman Segar')]);

            $menuData = [
                // Martabak Manis
                ['cat_id' => $catManis->id, 'name' => 'Manis Original Cokelat Kacang', 'price' => 25000, 'desc' => 'Adonan lembut dengan cokelat meses dan kacang sangrai.', 'options' => ['Biasa', 'Pakai Wijen'], 'img' => 'products/martabak-hening/manis-01.jpg'],
                ['cat_id' => $catManis->id, 'name' => 'Manis Keju Susu', 'price' => 28000, 'desc' => 'Keju cheddar parut dengan susu kental manis.', 'options' => ['Extra Keju'], 'img' => 'products/martabak-hening/manis-02.jpg'],
                ['cat_id' => $catManis->id, 'name' => 'Manis Cokelat Wijen', 'price' => 26000, 'desc' => 'Meses cokelat dengan taburan wijen gurih.', 'options' => [], 'img' => 'products/martabak-hening/manis-03.jpg'],
                ['cat_id' => $catManis->id, 'name' => 'Manis Keju Cokelat', 'price' => 30000, 'desc' => 'Perpaduan keju dan cokelat lumer.', 'options' => ['Tipis', 'Tebal'], 'img' => 'products/martabak-hening/manis-04.jpg'],
                ['cat_id' => $catManis->id, 'name' => 'Manis Pandan Keju', 'price' => 32000, 'desc' => 'Adonan pandan dengan topping keju melimpah.', 'options' => [], 'img' => 'products/martabak-hening/manis-05.jpg'],
                // Martabak Telur
                ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam', 'price' => 22000, 'desc' => 'Isian ayam cincang gurih.', 'options' => ['2 Telur', '3 Telur', '4 Telur'], 'img' => 'products/martabak-hening/telur-01.jpg'],
                ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sapi', 'price' => 28000, 'desc' => 'Daging sapi berbumbu kari.', 'options' => ['2 Telur', '3 Telur'], 'img' => 'products/martabak-hening/telur-02.jpg'],
                ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Bebek', 'price' => 30000, 'desc' => 'Telur bebek lebih gurih.', 'options' => [], 'img' => 'products/martabak-hening/telur-03.jpg'],
                // Minuman
                ['cat_id' => $catMinum->id, 'name' => 'Teh Tarik Hangat', 'price' => 8000, 'desc' => 'Teh susu khas berbusa.', 'options' => [], 'img' => 'products/martabak-hening/minum-01.jpg'],
                ['cat_id' => $catMinum->id, 'name' => 'Es Teh Manis', 'price' => 6000, 'desc' => 'Teh manis segar.', 'options' => [], 'img' => 'products/martabak-hening/minum-03.jpg'],
            ];

            foreach ($menuData as $item) {
                $product = Product::create([
                    'category_id' => $item['cat_id'],
                    'name' => $item['name'],
                    'description' => $item['desc'],
                    'image' => $item['img'],
                    'has_variants' => !empty($item['options']),
                    'is_active' => true,
                ]);

                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionName) {
                        $product->variants()->create([
                            'name' => $optionName,
                            'price' => $item['price'],
                            'stock' => 100,
                        ]);
                    }
                } else {
                    $product->variants()->create([
                        'name' => 'Regular',
                        'price' => $item['price'],
                        'stock' => 100,
                    ]);
                }
            }
        });
    }
}
