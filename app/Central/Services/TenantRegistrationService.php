<?php

namespace App\Central\Services;

use App\Central\Models\Tenant;
use App\Central\Models\TenantRegistration;
use App\Central\Models\User;
use App\Shared\Mail\SystemEmail;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TenantRegistrationService
{
    /**
     * Selesaikan pendaftaran tenant (buat database, user, kirim email).
     */
    public function completeRegistration(TenantRegistration $registration): void
    {
        try {
            $domainUrl = $registration->tenant_id . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
            
            Artisan::call('tenant:create', [
                'name' => $registration->store_name,
                '--id' => $registration->tenant_id,
                '--type' => $registration->store_type,
                '--domain' => $domainUrl,
                '--plan' => $registration->plan,
            ]);

            $plainPassword = $registration->password; // Retrieve plain password
            $tenant = Tenant::find($registration->tenant_id);
            $tenant?->run(function () use ($registration, $plainPassword) {
                User::firstOrCreate(
                    ['email' => $registration->email],
                    [
                        'name' => $registration->owner_name,
                        'password' => $plainPassword, // Set plain password (Laravel casts handles the hashing)
                        'role' => 'manager'
                    ]
                );
            });

            // Securely hash the password inside the central DB now that store is ready
            $registration->update([
                'status' => 'created',
                'password' => Hash::make($plainPassword)
            ]);

            // Send Welcome Email
            $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
            $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ({$registration->store_name}) telah selesai disiapkan dengan Paket " . ucfirst($registration->plan) . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://{$domainUrl}/auth/login\nEmail: {$registration->email}\nPassword: {$plainPassword}\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

            Mail::to($registration->email)->send(
                new SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://{$domainUrl}/auth/login")
            );

            Log::info('[TenantRegistrationService] Tenant Registration Success', ['tenant_id' => $registration->tenant_id]);
        } catch (Exception $e) {
            Log::error('[TenantRegistrationService] Failed to create tenant after payment', ['error' => $e->getMessage()]);

            // Send Failure Email
            $emailTitle = "Pendaftaran Toko Gagal";
            $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda. Namun, mohon maaf terjadi kesalahan sistem saat menyiapkan toko Anda ({$registration->store_name}). Tim kami sedang menelusuri masalah ini secara manual.\n\nSilakan hubungi tim support kami dengan melampirkan email ini agar segera ditindaklanjuti.\n\nSalam,\nTim Pakaiapp";

            try {
                Mail::to($registration->email)->send(
                    new SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                );
            } catch (Exception $mailEx) {
                Log::error('[TenantRegistrationService] Failed to send failure email: ' . $mailEx->getMessage());
            }
        }
    }
}
