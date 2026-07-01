<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRegistration extends Model
{
    protected $fillable = [
        'invoice_code',
        'owner_name',
        'email',
        'password',
        'store_name',
        'store_type',
        'tenant_id',
        'whatsapp',
        'plan',
        'amount',
        'status',
        'payment_method',
        'snap_token',
        'duitku_payment_url',
        'duitku_reference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];
}
