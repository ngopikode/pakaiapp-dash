<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class TenantUser extends Authenticatable
{
    use Notifiable;

    // Paksa model ini baca tabel 'users' (bukan 'tenant_users')
    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke tabel orders (Satu kasir bisa melayani banyak transaksi)
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
