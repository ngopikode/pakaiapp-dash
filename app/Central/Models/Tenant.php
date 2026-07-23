<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

#[Fillable([
    'id',
    'user_id',
    'subscription_plan',
    'trial_ends_at',
    'is_active',
    'data',
    'store_type',
    'created_at',
    'updated_at',
])]
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

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
