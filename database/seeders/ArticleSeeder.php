<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => '5 Alasan Mengapa Aplikasi Kasir Tanpa Biaya Langganan Lebih Menguntungkan untuk UMKM',
                'slug' => 'aplikasi-kasir-tanpa-biaya-langganan-umkm',
                'excerpt' => 'Menjalankan bisnis UMKM membutuhkan efisiensi. Temukan mengapa pindah ke aplikasi kasir tanpa biaya langganan bulanan bisa menyelamatkan arus kas bisnis Anda.',
                'content' => '<h2>Mengapa Harus Membayar Bulanan Jika Ada yang Gratis?</h2><p>Bagi pelaku UMKM, biaya operasional sekecil apapun sangat berarti. Salah satu pengeluaran rutin yang sering tidak disadari adalah biaya langganan aplikasi kasir (POS). Banyak provider POS yang mengenakan tarif bulanan ratusan ribu rupiah. Padahal, saat ini sudah ada <strong>aplikasi kasir tanpa biaya langganan</strong> yang fiturnya tidak kalah lengkap.</p><h3>1. Menghemat Arus Kas (Cash Flow)</h3><p>Biaya langganan bulanan mungkin terlihat kecil di awal. Tapi bayangkan jika Anda kalikan setahun. Uang tersebut sebenarnya bisa digunakan untuk modal iklan atau penambahan stok barang.</p><h3>2. Bayar Hanya Saat Ada Transaksi (Pay as You Go)</h3><p>Model bisnis terbaru seperti Pakaiapp menggunakan sistem <em>pay per transaction</em>. Artinya, jika restoran atau toko Anda sedang sepi, Anda tidak perlu membayar sepeser pun. Anda baru membayar biaya kecil (misal Rp 300) per transaksi sukses.</p><h3>3. Tidak Ada Biaya Tersembunyi</h3><p>Banyak aplikasi membatasi fitur (seperti jumlah produk atau laporan) di versi gratisnya. Pastikan Anda memilih platform yang memberikan semua fitur premium di awal tanpa syarat langganan.</p><p><strong>Kesimpulan:</strong> Saatnya beralih ke aplikasi kasir modern yang memihak pada UMKM.</p>',
                'meta_title' => 'Aplikasi Kasir Tanpa Biaya Langganan Terbaik 2024',
                'meta_description' => 'Cari tahu mengapa UMKM harus pindah ke aplikasi kasir tanpa biaya langganan. Lebih hemat dan bayar hanya saat transaksi sukses.',
                'published_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Keuntungan Menggunakan Aplikasi Kasir Berbasis Web untuk Bisnis Ritel & F&B',
                'slug' => 'keuntungan-aplikasi-kasir-berbasis-web',
                'excerpt' => 'Aplikasi kasir berbasis web (Cloud POS) memungkinkan Anda memantau bisnis dari mana saja tanpa perlu install aplikasi berat di HP atau tablet.',
                'content' => '<h2>Mengenal Kasir Berbasis Web (Cloud POS)</h2><p>Dulu, sistem kasir identik dengan komputer besar dan software yang harus di-install. Sekarang, dengan adanya teknologi Cloud, lahirlah <strong>aplikasi kasir berbasis web</strong>. Anda hanya butuh browser (Google Chrome, Safari) untuk mulai berjualan.</p><h3>Kelebihan Kasir Web Base:</h3><ul><li><strong>Tanpa Perlu Install:</strong> Hemat memori (storage) HP atau tablet Anda. Cukup buka URL, login, dan kasir siap digunakan.</li><li><strong>Pantau dari Mana Saja:</strong> Karena data tersimpan di Cloud, owner bisa melihat laporan penjualan real-time dari rumah menggunakan laptop, sementara karyawan menjaga toko menggunakan tablet.</li><li><strong>Cross-Platform:</strong> Bisa dibuka di Android, iOS (iPhone/iPad), Windows, maupun Mac. Anda tidak perlu membeli device khusus kasir yang mahal.</li></ul><p>Pakaiapp adalah salah satu contoh terbaik dari aplikasi kasir berbasis web yang sangat ringan dan mudah digunakan untuk segala jenis device.</p>',
                'meta_title' => 'Keunggulan Aplikasi Kasir Berbasis Web (Cloud POS)',
                'meta_description' => 'Mengapa aplikasi kasir berbasis web lebih unggul? Pantau bisnis real-time, tanpa install, dan bisa dibuka dari device mana saja.',
                'published_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tips Memilih Aplikasi Kasir untuk Cafe dan Restoran',
                'slug' => 'tips-memilih-aplikasi-kasir-cafe-restoran',
                'excerpt' => 'Jangan salah pilih sistem POS! Inilah fitur-fitur wajib yang harus ada di aplikasi kasir cafe dan restoran Anda agar operasional lebih lancar.',
                'content' => '<h2>Fitur Wajib Aplikasi Kasir Cafe (F&B)</h2><p>Bisnis Food & Beverage (F&B) memiliki alur kerja yang jauh lebih kompleks dibandingkan toko ritel biasa. Oleh karena itu, mencari <strong>aplikasi kasir cafe</strong> yang tepat adalah kunci kesuksesan operasional.</p><h3>1. Manajemen Meja dan Pesanan (Self-Order)</h3><p>Fitur pesanan langsung dari meja (Scan QR) sangat tren saat ini. Pelanggan bisa scan, lihat menu, dan pesan sendiri tanpa harus antre di kasir. Ini mengurangi beban kerja waiter/pelayan.</p><h3>2. Varian Menu dan Topping</h3><p>Pastikan aplikasi pos restoran Anda mendukung penambahan varian. Misalnya menu "Es Kopi Susu" dengan pilihan ukuran (Reguler/Large) dan ekstra topping (Boba/Jelly).</p><h3>3. Sistem Dapur (Kitchen Display / Split Order)</h3><p>Ketika pesanan masuk, kasir harus bisa mencetak atau mengirim struk langsung ke bagian dapur atau bar agar pesanan segera diproses tanpa harus mengantar kertas struk secara manual.</p><p>Apakah Anda mencari aplikasi dengan semua fitur di atas tapi tanpa biaya langganan? Pakaiapp bisa menjadi solusi andalan cafe Anda.</p>',
                'meta_title' => 'Tips Memilih Aplikasi Kasir Cafe & Restoran (F&B)',
                'meta_description' => 'Panduan lengkap memilih aplikasi kasir untuk cafe dan restoran. Fitur wajib seperti Self Order QR dan manajemen topping harus ada!',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        \App\Central\Models\Article::insert($articles);
    }
}
