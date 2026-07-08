<?php

return [
    'provider' => env('SHIPPING_PROVIDER', 'inpost'),

    'inpost' => [
        'api_token' => env('INPOST_API_TOKEN', ''),
        'base_url' => env('INPOST_BASE_URL', 'https://api.inpost.pl'),
    ],

    'dpd' => [
        'api_key' => env('DPD_API_KEY', ''),
        'login' => env('DPD_LOGIN', ''),
        'password' => env('DPD_PASSWORD', ''),
        'base_url' => env('DPD_BASE_URL', 'https://dpd.com.pl/api'),
    ],

    'methods' => [
        'inpost_locker' => [
            'label' => 'Paczkomat InPost',
            'price_grosze' => 1290,
            'provider' => 'inpost',
        ],
        'inpost_courier' => [
            'label' => 'Kurier InPost',
            'price_grosze' => 1590,
            'provider' => 'inpost',
        ],
        'dpd_locker' => [
            'label' => 'Paczkomat DPD',
            'price_grosze' => 1390,
            'provider' => 'dpd',
        ],
        'dpd_courier' => [
            'label' => 'Kurier DPD',
            'price_grosze' => 1690,
            'provider' => 'dpd',
        ],
        'pickup' => [
            'label' => 'Odbiór osobisty',
            'price_grosze' => 0,
            'provider' => null,
        ],
    ],
];
