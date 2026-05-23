<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'user_id',
        'subscription_plan',
        'trial_ends_at',
        'is_active',
        'data',
        'store_type',
        'created_at',
        'updated_at'
    ];

    /**
     * Daftarkan kolom kustom di sini agar Tenancy tahu ini adalah kolom nyata
     * di tabel database, bukan bagian dari kolom JSON 'data'.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'store_type', // <-- Tambahkan baris ini
        ];
    }
}
