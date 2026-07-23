<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
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
])]
#[Hidden(['password'])]
class TenantRegistration extends Model
{
}
