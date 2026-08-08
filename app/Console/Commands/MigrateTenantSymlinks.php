<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('tenant:migrate-storage')]
#[Description('Migrasi satu-kali untuk memindahkan folder tenant_* ke dalam folder tenants/ (Wajib dijalankan sebagai www-data)')]
class MigrateTenantSymlinks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Pastikan yang menjalankan command ini adalah www-data atau root
        $currentUser = exec('whoami');
        if (!in_array($currentUser, ['www-data', 'root'])) {
            $this->error("TOLAK: Command ini WAJIB dijalankan sebagai 'www-data' atau 'root'. (Saat ini: {$currentUser})");
            $this->line("Cara menjalankan: sudo -u www-data php artisan tenant:migrate-storage");
            return self::FAILURE;
        }

        $this->info("Memulai migrasi folder storage tenant...");

        $storagePath = storage_path();
        $publicPath = public_path();

        $tenantsStorageDir = $storagePath . '/tenants';
        $tenantsPublicDir = $publicPath . '/tenants';

        // 2. Buat folder penampung jika belum ada
        if (!File::isDirectory($tenantsStorageDir)) {
            File::makeDirectory($tenantsStorageDir, 0775, true);
            $this->info("Dibuat: {$tenantsStorageDir}");
        }

        if (!File::isDirectory($tenantsPublicDir)) {
            File::makeDirectory($tenantsPublicDir, 0775, true);
            $this->info("Dibuat: {$tenantsPublicDir}");
        }

        // 3. Pindahkan (Migrasi) folder storage/tenant_X ke storage/tenants/X
        $directories = File::directories($storagePath);
        $migratedCount = 0;

        foreach ($directories as $dir) {
            $folderName = basename($dir);

            if (str_starts_with($folderName, 'tenant_')) {
                $tenantId = str_replace('tenant_', '', $folderName);
                $newPath = $tenantsStorageDir . '/' . $tenantId;

                if (File::isDirectory($newPath)) {
                    $this->warn("Skip: tenants/{$tenantId} sudah ada, hapus dulu jika ingin overwrite.");
                    continue;
                }

                $this->line("Memindahkan folder storage: {$folderName} -> tenants/{$tenantId}");
                File::move($dir, $newPath);
                $migratedCount++;
            }
        }

        // 4. Hapus symlink usang di public/tenant_*
        $this->info("Menghapus symlink usang di folder public...");
        $filesAndDirsInPublic = File::glob($publicPath . '/tenant_*');
        
        foreach ($filesAndDirsInPublic as $item) {
            if (is_link($item) || is_dir($item)) {
                $this->line("Menghapus: " . basename($item));
                File::delete($item); // Works for symlinks
                if (is_dir($item)) {
                    File::deleteDirectory($item);
                }
            }
        }

        $this->info("Berhasil memigrasikan {$migratedCount} folder tenant.");
        $this->info("PENTING: Pastikan Anda sudah mengubah config/tenancy.php 'suffix_base' menjadi 'tenants/'.");
        $this->info("Setelah itu, jalankan: php artisan tenants:symlink");
        
        return self::SUCCESS;
    }
}
