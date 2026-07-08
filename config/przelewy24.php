<?php

return [
    'merchant_id' => env('P24_MERCHANT_ID', ''),
    'pos_id' => env('P24_POS_ID', ''),
    'crc_key' => env('P24_CRC_KEY', ''),
    'api_key' => env('P24_API_KEY', ''),
    'base_url' => env('P24_BASE_URL', 'https://sandbox.przelewy24.pl'),
    'return_url' => env('P24_RETURN_URL', env('APP_URL').'/checkout/confirmation'),
    'status_url' => env('P24_STATUS_URL', env('APP_URL').'/payment/przelewy24/webhook'),
];
