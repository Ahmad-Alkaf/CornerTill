<?php

/*
|--------------------------------------------------------------------------
| Feature switches
|--------------------------------------------------------------------------
|
| Launch switches. They hide features without removing their code.
| Read them with config('features.*'), never with env(), because
| `php artisan config:cache` runs in production.
|
*/

return [

    /*
    | PAYMENTS_ENABLED=false (default): free beta. Paddle plans, pricing,
    | subscription routes, the Paddle webhook and Paddle.js are off, and
    | every business uses the "beta" resource limits (config/constants.php).
    | PAYMENTS_ENABLED=true: the Paddle subscription flow works as before.
    */
    'payments_enabled' => (bool) env('PAYMENTS_ENABLED', false),

    /*
    | Social login providers. A disabled provider hides its button, and its
    | /auth/{provider}/redirect and /auth/{provider}/callback routes return 404.
    */
    'social' => [
        'google' => (bool) env('SOCIAL_GOOGLE_ENABLED', true),
        'github' => (bool) env('SOCIAL_GITHUB_ENABLED', true),
        'x' => (bool) env('SOCIAL_X_ENABLED', false),
    ],

];
