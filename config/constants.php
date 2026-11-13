<?php
return [
    // A deleted business is kept this number of days, so that the Owner can ask
    // support to restore it (php artisan business:restore {id}). Then the daily
    // job `business:purge-deleted` deletes all its data permanently.
    // The privacy policy, the terms, and the delete-business text of the business
    // page (DeleteBusinessSection.tsx) state this number: change them together.
    'deletedBusinessRetentionDays' => 30,
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
