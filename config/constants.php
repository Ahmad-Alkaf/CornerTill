<?php
return [
    // Limits for every business during the free beta (PAYMENTS_ENABLED=false).
    // Default: the same numbers as the Basic plan.
    'betaResources' => [
        'accounts' => (int) env('BETA_MAX_ACCOUNTS', 2),
        'products' => (int) env('BETA_MAX_PRODUCTS', 500),
        'bills' => (int) env('BETA_MAX_BILLS_PER_MONTH', 15_000),
    ],
    'planResources' => [
        'basic' => [
            'accounts' => 2,
            'products' => 500,
            'bills' => 15_000, //20 per hour
        ],
        'enhanced' => [
            'accounts' => 10,
            'products' => 10_000,
            'bills' => 75_000, //104 per hour
        ],
        'advanced' => [
            'accounts' => 50,
            'products' => 100_000,
            'bills' => 500_000, //694 per hour
        ],
    ],
];
