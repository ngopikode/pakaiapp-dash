<?php

$enabled = filter_var(env('MIDTRANS_ENABLED', true), FILTER_VALIDATE_BOOLEAN);

return [
    'enabled' => $enabled,
    'server_key' => $enabled ? env('MIDTRANS_SERVER_KEY', '') : '',
    'client_key' => $enabled ? env('MIDTRANS_CLIENT_KEY', '') : '',
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];
